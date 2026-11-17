<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Session;

class TopologyController extends Controller
{
   private function stateId()
    {
        Session::put('user', Auth::User());
        $user = Session::get('user');
        

        if (!$user || empty($user->state_id)) {
            abort(403, 'No state assigned to this user');
        }
        return (int) $user->state_id;
    }
    public function index()
    {
        $states    = [1 => 'West Bengal', 2 => 'Andaman & Nicobar'];
        $stateId   = $this->stateId();
        $stateName = isset($states[$stateId]) ? $states[$stateId] : 'State ' . $stateId;

        return view('admin.topology.index', compact('stateName'));
    }
    /* ---------- Level 1: districts ---------- */
    public function state()
    {
        $rows = DB::select(
            "SELECT d.cluster_name AS name, COUNT(DISTINCT d.block_name) AS blocks, " . $this->countSql() . "
             FROM hfcl_devices d
             WHERE d.cluster_name IS NOT NULL AND d.state_id = ?
             GROUP BY d.cluster_name
             ORDER BY d.cluster_name",
            [$this->stateId()]
        );
        $alarms = $this->alarmCounts('cluster_name');

        $out = [];
        foreach ($rows as $r) {
            $out[] = ['name' => $r->name, 'blocks' => (int) $r->blocks]
                + $this->counts($r)
                + ['alarms' => isset($alarms[$r->name]) ? (int) $alarms[$r->name] : 0];
        }

        return response()->json(['clusters' => $out, 'last_sync' => $this->lastSync()]);
    }

    /* ---------- Level 2: blocks in one district ---------- */
    public function cluster(Request $request)
    {
        $name = strtolower(trim((string) $request->input('name')));

        $rows = DB::select(
            "SELECT d.block_name AS name, " . $this->countSql() . ",
                    MAX(CASE WHEN d.level LIKE '%Block%' THEN d.effective_status END) AS router_status
             FROM hfcl_devices d
             WHERE d.cluster_name = ? AND d.state_id = ?
             GROUP BY d.block_name
             ORDER BY d.block_name",
            [$name, $this->stateId()]    
        );
        $alarms = $this->alarmCounts('block_name', $name);

        $out = [];
        foreach ($rows as $r) {
            $out[] = ['name' => $r->name, 'router_status' => $r->router_status ?: 'unknown']
                + $this->counts($r)
                + ['alarms' => isset($alarms[$r->name]) ? (int) $alarms[$r->name] : 0];
        }

        return response()->json(['cluster' => $name, 'blocks' => $out, 'last_sync' => $this->lastSync()]);
    }

    /* ---------- Level 3: one block graph ---------- */
    public function block(Request $request)
    {
        $cluster = strtolower(trim((string) $request->input('cluster')));
        $block   = strtolower(trim((string) $request->input('block')));

 
        $devices = DB::table('hfcl_devices')
            ->where('state_id', $this->stateId())
            ->where('cluster_name', $cluster)->where('block_name', $block)->get();

        $ids = [];
        foreach ($devices as $d) $ids[] = $d->device_id;

        if (!$ids) {
            return response()->json(['nodes' => [], 'links' => [], 'last_sync' => $this->lastSync()]);
        }

        $links = DB::table('hfcl_links')
            ->where(function ($q) use ($ids) {
                $q->whereIn('a_device', $ids)->orWhereIn('b_device', $ids);
            })->get();

        // Neighbours in other blocks (shown greyed out)
        $inBlock = array_flip($ids);
        $extIds  = [];
        foreach ($links as $l) {
            foreach ([$l->a_device, $l->b_device] as $x) {
                if (!isset($inBlock[$x])) $extIds[$x] = true;
            }
        }
        $external = $extIds
            ? DB::table('hfcl_devices')->whereIn('device_id', array_keys($extIds))->get()
            : [];

        // Active alarms of devices in this block
        $alarmRows = DB::table('hfcl_alarms')
            ->whereIn('device_id', $ids)->whereNull('cleared_at')
            ->orderBy('raised_at', 'desc')
            ->get(['device_id', 'alarm_type', 'resource', 'description', 'severity', 'raised_at']);

        $byDev = [];
        foreach ($alarmRows as $a) $byDev[$a->device_id][] = $a;

        // Attach interface-down alarms to the exact link (device + port)
        $portIdx = [];
        foreach ($links as $l) {
            foreach (explode(',', $l->a_port) as $p) if ($p !== '') $portIdx[$l->a_device . '|' . $p] = $l->link_key;
            foreach (explode(',', $l->b_port) as $p) if ($p !== '') $portIdx[$l->b_device . '|' . $p] = $l->link_key;
        }
        $linkAlarms = [];
        foreach ($alarmRows as $a) {
            $k = $a->device_id . '|' . $a->resource;
            if ($a->alarm_type === 'IFMGR_IF_DOWN' && isset($portIdx[$k])) {
                $linkAlarms[$portIdx[$k]][] = $a;
            }
        }

        // Nodes
        $nodes = [];
        foreach ($devices as $d) $nodes[] = $this->node($d, $byDev, false);

        $haveExt = [];
        foreach ($external as $d) {
            $nodes[] = $this->node($d, [], true);
            $haveExt[$d->device_id] = true;
        }
        foreach (array_keys($extIds) as $x) {            // neighbour not in HFCL device list
            if (!isset($haveExt[$x])) {
                $nodes[] = [
                    'id' => $x, 'name' => $x, 'level' => 'Unknown', 'status' => 'unknown',
                    'effective_status' => 'unknown', 'external' => true,
                    'alarm_count' => 0, 'max_severity' => null, 'alarms' => [],
                ];
            }
        }

        // Links
        $latestRun = DB::table('hfcl_devices')->max('run_id');
        $out = [];
        foreach ($links as $l) {
            $out[] = [
                'id'          => $l->link_key,
                'source'      => $l->a_device,
                'target'      => $l->b_device,
                'a_port'      => $l->a_port,
                'b_port'      => $l->b_port,
                'status'      => $l->status,
                'stale'       => $l->run_id !== $latestRun,   // last known, not in latest sync
                'last_change' => $l->last_change,
                'aerial_km'   => $l->aerial_km !== null ? (float) $l->aerial_km : null,
                'alarms'      => isset($linkAlarms[$l->link_key]) ? $linkAlarms[$l->link_key] : [],
            ];
        }

        return response()->json([
            'cluster'   => $cluster,
            'block'     => $block,
            'nodes'     => $nodes,
            'links'     => $out,
            'last_sync' => $this->lastSync(),
        ]);
    }

    /* ---------- helpers ---------- */

    private function countSql()
    {
        return "COUNT(*) AS devices,
                SUM(d.effective_status = 'up')      AS up,
                SUM(d.effective_status = 'down')    AS down,
                SUM(d.effective_status = 'unreach') AS unreach,
                SUM(d.effective_status = 'unknown') AS unknown";
    }

    private function counts($r)
    {
        $devices = (int) $r->devices;
        $up      = (int) $r->up;
        return [
            'devices' => $devices,
            'up'      => $up,
            'down'    => (int) $r->down,
            'unreach' => (int) $r->unreach,
            'unknown' => (int) $r->unknown,
            'up_pct'  => $devices ? round($up * 100 / $devices, 1) : 0,
        ];
    }

    private function alarmCounts($col, $cluster = null)
    {
        $q = DB::table('hfcl_alarms as a')
            ->join('hfcl_devices as d', 'd.device_id', '=', 'a.device_id')
            ->whereNull('a.cleared_at');
        $q->where('d.state_id', $this->stateId());;
        if ($cluster !== null) $q->where('d.cluster_name', $cluster);

        return $q->groupBy('d.' . $col)
            ->select(DB::raw('d.' . $col . ' AS k, COUNT(*) AS n'))
            ->pluck('n', 'k')->all();
    }

    private function node($d, array $byDev, $external)
    {
        $alarms = isset($byDev[$d->device_id]) ? $byDev[$d->device_id] : [];
        $max = null;
        foreach ($alarms as $a) {
            if ($a->severity === 'CRITICAL') { $max = 'CRITICAL'; break; }
            $max = $a->severity;
        }

        return [
            'id'               => $d->device_id,
            'name'             => $d->name,
            'level'            => $d->level,
            'status'           => $d->status,
            'effective_status' => $d->effective_status,
            'ip'               => $d->ip,
            'model'            => $d->model,
            'site'             => $d->site_name,
            'block'            => $d->block_name,
            'cluster'          => $d->cluster_name,
            'rings'            => json_decode($d->rings ?: '[]', true),
            'lat'              => $d->lat !== null ? (float) $d->lat : null,
            'lng'              => $d->lng !== null ? (float) $d->lng : null,
            'last_synced'      => $d->last_synced,
            'external'         => $external,
            'alarm_count'      => count($alarms),
            'max_severity'     => $max,
            'alarms'           => $alarms,
        ];
    }

    private function lastSync()
    {
        return DB::table('hfcl_snapshots')->where('endpoint', 'network_topology')->max('received_at');
    }
}