<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\EmployeeMaterialLedger;
use App\Provider;
use App\Material;
use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;

class SyncEmployeeMaterialLedger extends Command
{
    protected $signature = 'ledger:sync-employee-materials';

    protected $description = 'Sync employee material issue data from PM Tool';

    public function handle()
    {
        $this->info('Ledger sync started');
        $client = new Client([
            'timeout' => 60,
            'verify' => false 
        ]);

        try {
            $response = $client->get('https://projects.terasoftware.com/index.php/api/get_om_material_issue_list');
            $body = json_decode($response->getBody()->getContents(), true);

           if (!is_array($body) || !isset($body['data']) || !isset($body['status']) || $body['status'] != 1) {
                Log::error('Ledger API returned invalid or unsuccessful response', ['response' => $body]);
                return 0;
            }
            $inserted = 0;
            $updated  = 0;
            $skipped  = 0;

            foreach ($body['data'] as $row) {

                $employee = Provider::find($row['employee_id']);
                if (!$employee) {
                    Log::warning('Ledger sync: employee not found', ['employee_id' => $row['employee_id']]);
                    $skipped++;
                    continue;
                }

                $material = Material::where('code', $row['mat_code'])->first();
                if (!$material) {
                    Log::warning('Ledger sync: material not found', ['mat_code' => $row['mat_code']]);
                    $skipped++;
                    continue;
                }
                $isDrum   = !empty($row['drum_no']);
                $isSerial = !empty($row['serial_no']);
                $isLengthBased = ($row['issued_length'] > 0);

                $resolvedSerial = $isDrum
                    ? $row['drum_no']
                    : ($isSerial ? $row['serial_no'] : null);

                //    One ISSUE ledger row per (employee + material + indent)
                $matchKey = [
                    'employee_id'      => $employee->id,
                    'material_code'    => $row['mat_code'],
                    'indent_no'        => $row['indent_no'] ?? null,
                    'serial_number'    => $resolvedSerial, 
                    'transaction_type' => 'ISSUE',
                ];

                $syncPayload = [
                    'indent_no'       =>  $row['indent_no'] ?? null,
                    'employee_id'         => $employee->id,
                    'state_id'            => $employee->state_id,
                    'district_id'         => $employee->district_id,
                    'material_id'         => $material->id,
                    'material_code'       => $row['mat_code'],
                    'has_serial'    => ($isDrum || $isSerial) ? 1 : 0,
                    'serial_number' => $resolvedSerial,
                  
                    'quantity'            => $isLengthBased
                                                ? $row['balance_length']
                                                : $row['balance_qty'],

                    'original_issued_qty' => $isLengthBased
                                                ? $row['issued_length']
                                                : $row['issued_qty'],
                    'transferred_in_qty'  => $isLengthBased
                                                ? $row['transferred_in_length']
                                                : $row['transferred_in_qty'],
                    'transferred_out_qty' => $isLengthBased
                                                ? $row['transferred_out_length']
                                                : $row['transferred_out_qty'],
                ];


               $existing = EmployeeMaterialLedger::where($matchKey)->first();


                if ($existing) {
                    $existing->update($syncPayload);
                     $updated++;
                }else{
                    EmployeeMaterialLedger::create(array_merge($matchKey, [
                        'transaction_type' => 'ISSUE',
                        'issue_date'       => Carbon::now(),
                    ], $syncPayload));
                    $inserted++;
                }
            }

            $summary = "Ledger sync completed — inserted: {$inserted}, updated: {$updated}, skipped: {$skipped}";
            $this->info($summary);
            Log::info($summary);

        } catch (RequestException $e) {
            Log::error('Ledger sync failed', [
                'error' => $e->getMessage()
            ]);
        }

      
    }

  
}
