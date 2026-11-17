<?php

namespace App\Services;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

class HfclImporter
{
    // HFCL uses two names for the same district
    const CLUSTER_ALIAS = [
        'north 24 paraganas' => '24 paraganas north',
    ];
        // Districts that belong to Andaman & Nicobar (state_id 2); everything else is West Bengal (1)
    const ANDAMAN_CLUSTERS = ['north and middle andaman', 'south andamans', 'nicobars'];

    /**
     * Called when all parts of a run have arrived.
     */
    public function process($endpoint, $runId)
    {
        $rows = $this->loadRun($endpoint, $runId);

       
        switch ($endpoint) {
            case 'network_topology':
                return $this->importTopology($rows, $runId);
            case 'active_alarms':
                return $this->importAlarms($rows, $runId);
        }
        return null;   // other endpoints are added in later steps
    }

    /* ---------------- network-topology ---------------- */

    private function importTopology(array $rows, $runId)
    {
        $now    = date('Y-m-d H:i:s');
        $old    = DB::table('hfcl_devices')->pluck('status', 'device_id')->all();
        $devs   = [];
        $events = [];
        $coords = [];

        foreach ($rows as $d) {
            $id = isset($d['_id']) ? trim((string) $d['_id']) : '';
            if ($id === '') continue;

            $loc = (isset($d['location']) && is_array($d['location'])) ? $d['location'] : [];
            $cs  = (isset($d['connection_status']) && is_array($d['connection_status'])) ? $d['connection_status'] : [];
            $c   = (isset($loc['coordinates']) && is_array($loc['coordinates'])) ? $loc['coordinates'] : [];

            $lat = (isset($c[0]) && is_numeric($c[0]) && (float) $c[0] != 0) ? (float) $c[0] : null;
            $lng = (isset($c[1]) && is_numeric($c[1]) && (float) $c[1] != 0) ? (float) $c[1] : null;
            $coords[$id] = [$lat, $lng];

            $status = isset($cs['status']) ? strtolower(trim($cs['status'])) : 'unknown';
            if ($status === '') $status = 'unknown';

            $name = '';
            foreach (['device_name', 'hostname'] as $k) {
                if (!empty($d[$k]) && strtolower($d[$k]) !== 'unknown') { $name = $d[$k]; break; }
            }

            $devs[] = [
                'device_id'    => $id,
                'name'         => $name !== '' ? $name : $id,
                'level'        => isset($d['device_level']) ? (string) $d['device_level'] : '',
                'cluster_name' => $this->normCluster(isset($loc['cluster_name']) ? $loc['cluster_name'] : null),
                'block_name'   => $this->norm(isset($loc['block_name']) ? $loc['block_name'] : null),
                'site_name'    => $this->norm(isset($loc['site_name']) ? $loc['site_name'] : null),
                'rings'        => json_encode(isset($loc['ring_name']) && is_array($loc['ring_name']) ? $loc['ring_name'] : []),
                'lat'          => $lat,
                'lng'          => $lng,
                'ip'           => isset($d['ip_address']) ? (string) $d['ip_address'] : null,
                'mac'          => isset($d['mac_address']) ? strtolower((string) $d['mac_address']) : null,
                'model'        => isset($d['device_model']) ? (string) $d['device_model'] : null,
                'serial_no'    => isset($d['device_serial_number']) ? (string) $d['device_serial_number'] : null,
                'status'       => $status,
                'last_synced'  => $this->parseTime(isset($cs['last_synced']) ? $cs['last_synced'] : null),
                'run_id'       => $runId,
                'created_at'   => $now,
                'updated_at'   => $now,
                'circle'       => $this->norm(isset($loc['circle_name']) ? $loc['circle_name'] : null),
                'state_id'     => in_array($this->normCluster(isset($loc['cluster_name']) ? $loc['cluster_name'] : null), self::ANDAMAN_CLUSTERS, true) ? 2 : 1,
            ];

            if (isset($old[$id]) && $old[$id] !== $status) {
                $events[] = [
                    'device_id'   => $id,
                    'from_status' => $old[$id],
                    'to_status'   => $status,
                    'changed_at'  => $now,
                ];
            }
        }

        $this->upsertBatch('hfcl_devices',
            ['device_id', 'name', 'level', 'circle', 'state_id', 'cluster_name', 'block_name', 'site_name', 'rings', 'lat', 'lng',
             'ip', 'mac', 'model', 'serial_no', 'status', 'last_synced', 'run_id', 'created_at', 'updated_at'],
            $devs,
            ['name', 'level', 'circle', 'state_id', 'cluster_name', 'block_name', 'site_name', 'rings', 'lat', 'lng',
             'ip', 'mac', 'model', 'serial_no', 'status', 'last_synced', 'run_id', 'updated_at']);

        if ($events) {
            DB::table('hfcl_status_events')->insert($events);
        }

        $links = $this->importLinks($rows, $coords, $runId, $now);
        $this->classifyAll();

        return ['devices' => count($devs), 'links' => $links, 'status_changes' => count($events)];
    }

    private function importLinks(array $rows, array $coords, $runId, $now)
    {
        // Group LLDP entries by device pair (both sides collapse into one link)
        $pairs = [];
        foreach ($rows as $d) {
            $id = isset($d['_id']) ? trim((string) $d['_id']) : '';
            if ($id === '' || empty($d['lldp_neighbors']) || !is_array($d['lldp_neighbors'])) continue;

            foreach ($d['lldp_neighbors'] as $n) {
                if (!is_array($n)) continue;
                $rid = isset($n['remote_device_id']) ? trim((string) $n['remote_device_id']) : '';
                if ($rid === '' || $rid === $id) continue;

                

                $lp = $this->portName([isset($n['local_port_id']) ? $n['local_port_id'] : null]);
                $rp = $this->portName([
                    isset($n['remote_interface_name']) ? $n['remote_interface_name'] : null,
                    isset($n['remote_port_id']) ? $n['remote_port_id'] : null,
                ]);

                $isUp = strtolower(isset($n['local_oper_status']) ? $n['local_oper_status'] : '') === 'up'
                     && strtolower(isset($n['remote_oper_status']) ? $n['remote_oper_status'] : '') === 'up';

                if (strcmp($id, $rid) < 0) { $a = $id;  $b = $rid; $ap = $lp; $bp = $rp; }
                else                       { $a = $rid; $b = $id;  $ap = $rp; $bp = $lp; }

                $key = $a . '|' . $b;
                if (!isset($pairs[$key])) {
                    $pairs[$key] = ['a' => $a, 'b' => $b, 'ap' => [], 'bp' => [], 'up' => 0, 'down' => 0];
                }
                if ($ap !== '') $pairs[$key]['ap'][$ap] = true;
                if ($bp !== '') $pairs[$key]['bp'][$bp] = true;
                if ($isUp) { $pairs[$key]['up']++; } else { $pairs[$key]['down']++; }
            }
        }

        // Keep last_change when the status didn't change
        $oldLinks = [];
        foreach (DB::table('hfcl_links')->get(['link_key', 'status', 'last_change']) as $l) {
            $oldLinks[$l->link_key] = $l;
        }

        $out = [];
        foreach ($pairs as $key => $p) {
            $status = $p['down'] === 0 ? 'up' : ($p['up'] === 0 ? 'down' : 'partial');

            $lastChange = $now;
            if (isset($oldLinks[$key]) && $oldLinks[$key]->status === $status) {
                $lastChange = $oldLinks[$key]->last_change;
            }

            $km = null;
            if (isset($coords[$p['a']], $coords[$p['b']])
                && $coords[$p['a']][0] !== null && $coords[$p['b']][0] !== null) {
                $km = $this->aerialKm($coords[$p['a']][0], $coords[$p['a']][1], $coords[$p['b']][0], $coords[$p['b']][1]);
            }

            $out[] = [
                'link_key'    => $key,
                'a_device'    => $p['a'],
                'a_port'      => substr(implode(',', array_keys($p['ap'])), 0, 30),
                'b_device'    => $p['b'],
                'b_port'      => substr(implode(',', array_keys($p['bp'])), 0, 30),
                'status'      => $status,
                'last_change' => $lastChange,
                'aerial_km'   => $km,
                'run_id'      => $runId,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        $this->upsertBatch('hfcl_links',
            ['link_key', 'a_device', 'a_port', 'b_device', 'b_port', 'status', 'last_change', 'aerial_km', 'run_id', 'created_at', 'updated_at'],
            $out,
            ['a_port', 'b_port', 'status', 'last_change', 'aerial_km', 'run_id', 'updated_at']);

        
                // Links not reported in this run:
        //  - both devices reachable  -> link really removed  -> delete
        //  - a device is unreachable -> keep as last known topology, mark down
        $reach = array_flip(
            DB::table('hfcl_devices')->where('status', 'reachable')->pluck('device_id')->all()
        );

        foreach (DB::table('hfcl_links')->where('run_id', '!=', $runId)->get(['id', 'a_device', 'b_device', 'status']) as $l) {
            if (isset($reach[$l->a_device], $reach[$l->b_device])) {
                DB::table('hfcl_links')->where('id', $l->id)->delete();
            } elseif ($l->status !== 'down') {
                DB::table('hfcl_links')->where('id', $l->id)->update([
                    'status'      => 'down',
                    'last_change' => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        return count($out);
    }

    /**
     * up      = device reachable
     * down    = unreachable, but a neighbour is reachable (the failure is here)
     * unreach = unreachable and all neighbours unreachable (cut off behind another failure)
     */
    private function classifyAll()
    {
        $devices = [];
        foreach (DB::table('hfcl_devices')->get(['device_id', 'status', 'level']) as $d) {
            $devices[$d->device_id] = $d;
        }

        $adj = [];
        foreach (DB::table('hfcl_links')->get(['a_device', 'b_device']) as $l) {
            $adj[$l->a_device][] = $l->b_device;
            $adj[$l->b_device][] = $l->a_device;
        }

        $groups = ['up' => [], 'down' => [], 'unreach' => [], 'unknown' => []];
        foreach ($devices as $id => $d) {
            if ($d->status === 'reachable')   { $groups['up'][] = $id; continue; }
            if ($d->status !== 'unreachable') { $groups['unknown'][] = $id; continue; }

            $neighbours = isset($adj[$id]) ? $adj[$id] : [];
            $liveNbr = false;
            foreach ($neighbours as $n) {
                if (isset($devices[$n]) && $devices[$n]->status === 'reachable') { $liveNbr = true; break; }
            }
            $isBlockRouter = stripos($d->level, 'block') !== false;

            $groups[($liveNbr || $isBlockRouter || !$neighbours) ? 'down' : 'unreach'][] = $id;
        }

        foreach ($groups as $status => $ids) {
            foreach (array_chunk($ids, 500) as $chunk) {
                DB::table('hfcl_devices')->whereIn('device_id', $chunk)->update(['effective_status' => $status]);
            }
        }
    }

        /* ---------------- active alarms ---------------- */

    private function importAlarms(array $rows, $runId)
    {
        $now = date('Y-m-d H:i:s');
        $out = [];

        foreach ($rows as $a) {
            if (!is_array($a) || empty($a['device_id']) || empty($a['id'])) continue;

            $key    = (string) $a['id'];                                   // e.g. IFMGR_IF_DOWN::po2
            $type   = strstr($key, ':', true) ?: $key;                     // IFMGR_IF_DOWN
            $raised = $this->parseTime(isset($a['alarm reported timestamp']) ? $a['alarm reported timestamp'] : null);

            $out[] = [
                'device_id'     => (string) $a['device_id'],
                'alarm_key'     => substr($key, 0, 255),
                'alarm_type'    => substr($type, 0, 60),
                'resource'      => isset($a['resource']) ? substr((string) $a['resource'], 0, 255) : null,
                'description'   => isset($a['alarm description']) ? (string) $a['alarm description'] : null,
                'severity'      => strtoupper(isset($a['alarm severity']) && $a['alarm severity'] !== '' ? $a['alarm severity'] : 'UNKNOWN'),
                'category'      => isset($a['type id']) ? substr((string) $a['type id'], 0, 20) : null,
                'acknowledged'  => strtolower(isset($a['acknowledged']) ? (string) $a['acknowledged'] : '') === 'true' ? 1 : 0,
                'occurrence'    => isset($a['occurrence']) ? (int) $a['occurrence'] : 1,
                // unique key needs a value; unparseable times get a fixed placeholder
                'raised_at'     => $raised ?: '2000-01-01 00:00:00',
                'first_seen_at' => $now,
                'last_seen_at'  => $now,
                'cleared_at'    => null,
                'run_id'        => $runId,
            ];
        }

        $this->upsertBatch('hfcl_alarms',
            ['device_id', 'alarm_key', 'alarm_type', 'resource', 'description', 'severity', 'category',
             'acknowledged', 'occurrence', 'raised_at', 'first_seen_at', 'last_seen_at', 'cleared_at', 'run_id'],
            $out,
            // first_seen_at is NOT updated, so it keeps the time we first saw the alarm
            ['alarm_type', 'resource', 'description', 'severity', 'category',
             'acknowledged', 'occurrence', 'last_seen_at', 'cleared_at', 'run_id']);

        // Alarms no longer reported by HFCL -> cleared now
        $cleared = DB::table('hfcl_alarms')
            ->whereNull('cleared_at')
            ->where('run_id', '!=', $runId)
            ->update(['cleared_at' => $now]);

        return ['active' => count($out), 'cleared' => $cleared];
    }

    /* ---------------- helpers ---------------- */

    private function loadRun($endpoint, $runId)
    {
        $rows = [];
        $parts = DB::table('hfcl_snapshots')
            ->where('endpoint', $endpoint)->where('run_id', $runId)
            ->orderBy('part')->pluck('payload');

        foreach ($parts as $payload) {
            $data = json_decode($payload, true);
            if (is_array($data)) {
                foreach ($data as $r) $rows[] = $r;
            }
        }
        return $rows;
    }

    private function upsertBatch($table, array $cols, array $rows, array $update, $size = 200)
    {
        $updateSql = implode(',', array_map(function ($c) { return "`$c`=VALUES(`$c`)"; }, $update));

        foreach (array_chunk($rows, $size) as $chunk) {
            $placeholders = [];
            $bindings = [];
            foreach ($chunk as $r) {
                $placeholders[] = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
                foreach ($cols as $c) $bindings[] = $r[$c];
            }
            DB::statement(
                "INSERT INTO `$table` (`" . implode('`,`', $cols) . "`) VALUES " . implode(',', $placeholders)
                . " ON DUPLICATE KEY UPDATE $updateSql",
                $bindings
            );
        }
    }

        // First value that is a real port name (xe9, po1, ge1), skipping MAC addresses
    private function portName(array $candidates)
    {
        foreach ($candidates as $v) {
            $v = trim((string) $v);
            if ($v === '') continue;
            if (preg_match('/^([0-9a-f]{2}[:\-]){5}[0-9a-f]{2}$/i', $v)) continue;   // MAC
            return $v;
        }
        return '';
    }

    private function norm($s)
    {
        if ($s === null) return null;
        $s = strtolower(trim((string) $s));
        return $s === '' ? null : $s;
    }

    private function normCluster($s)
    {
        $n = $this->norm($s);
        return ($n !== null && isset(self::CLUSTER_ALIAS[$n])) ? self::CLUSTER_ALIAS[$n] : $n;
    }

    // "Fri Nov 06 2026 12:53:59 IST" or ISO formats -> app timezone
    public function parseTime($s)
    {
        if (!is_string($s) || trim($s) === '') return null;
        $ist = new DateTimeZone('Asia/Kolkata');

        if (preg_match('/([A-Z][a-z]{2}) (\d{1,2}) (\d{4}) (\d{2}:\d{2}:\d{2})/', $s, $m)) {
            $dt = DateTime::createFromFormat('M d Y H:i:s', "$m[1] $m[2] $m[3] $m[4]", $ist);
        } else {
            $dt = date_create(trim(str_replace('IST', '', $s)), $ist);
        }
        if (!$dt) return null;

        $dt->setTimezone(new DateTimeZone(config('app.timezone')));
        return $dt->format('Y-m-d H:i:s');
    }

    private function aerialKm($lat1, $lng1, $lat2, $lng2)
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) * sin($dLng / 2);
        return round(6371 * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }
}