<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HfclIngestController extends Controller
{
    private $allowedEndpoints = [
        'clusters_list',
        'network_topology',
        'device_list',
        'active_alarms',
        'availability',
    ];

    public function store(Request $request)
    {
        // 1. Token check
        $token = (string) config('services.hfcl.ingest_token');
        if ($token === '' || !hash_equals($token, (string) $request->bearerToken())) {
            return response()->json(['ok' => false, 'error' => 'unauthorized'], 401);
        }

        // 2. IP check (seen_ip is returned to help setup; remove it once working)
        $allowedIp = config('services.hfcl.sync_server_ip');
        if ($allowedIp && $request->ip() !== $allowedIp) {
            return response()->json([
                'ok'      => false,
                'error'   => 'forbidden',
                'seen_ip' => $request->ip(),
            ], 403);
        }

        // 3. Validate payload
        $this->validate($request, [
            'endpoint'    => 'required|string|max:50',
            'run_id'      => 'required|string|max:20',
            'part'        => 'required|integer|min:1',
            'total_parts' => 'required|integer|min:1',
            'data'        => 'present|array',
        ]);

        $endpoint   = $request->input('endpoint');
        $runId      = $request->input('run_id');
        $part       = (int) $request->input('part');
        $totalParts = (int) $request->input('total_parts');

        if (!in_array($endpoint, $this->allowedEndpoints, true)) {
            return response()->json(['ok' => false, 'error' => 'unknown endpoint'], 422);
        }

        // 4. Store this part (re-sending the same part overwrites it)
        $now = date('Y-m-d H:i:s');
        DB::statement(
            'INSERT INTO hfcl_snapshots (endpoint, run_id, part, total_parts, payload, received_at)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE total_parts = VALUES(total_parts),
                                     payload     = VALUES(payload),
                                     received_at = VALUES(received_at)',
            [$endpoint, $runId, $part, $totalParts, json_encode($request->input('data')), $now]
        );

        // 5. Is the whole run here?
        $received = DB::table('hfcl_snapshots')
            ->where('endpoint', $endpoint)
            ->where('run_id', $runId)
            ->count();

        $complete = $received >= $totalParts;

        if ($complete) {
            // Keep only the latest complete run for this endpoint
            DB::table('hfcl_snapshots')
                ->where('endpoint', $endpoint)
                ->where('run_id', '!=', $runId)
                ->delete();

            // Turn the complete run into devices / links / alarms
            try {
                $import = (new \App\Services\HfclImporter)->process($endpoint, $runId);
            } catch (\Throwable $e) {
                \Log::error('HFCL import failed (' . $endpoint . '): ' . $e->getMessage());
                $import = ['error' => $e->getMessage()];
            }
        }

        return response()->json([
            'ok'          => true,
            'endpoint'    => $endpoint,
            'run_id'      => $runId,
            'part'        => $part,
            'received'    => $received,
            'total_parts' => $totalParts,
            'complete'    => $complete,
            'import'      => isset($import) ? $import : null,
        ]);
    }
}