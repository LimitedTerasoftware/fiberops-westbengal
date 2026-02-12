<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\EmployeeMaterialLedger;
use App\Provider;
use App\Material;
use Illuminate\Support\Facades\Log;

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

            if (!is_array($body) || !isset($body['data'])) {
                Log::error('Ledger API returned invalid JSON', [
                    'response' => $body
                ]);
                return 0;
            }

            foreach ($body['data'] as $row) {


                $exists = EmployeeMaterialLedger::where([
                    'request_id'       => $row['request_id'],
                    'issued_item_id'   => $row['issued_item_id'],
                    'transaction_type'=> 'ISSUE'
                ])->exists();

                if ($exists) {
                    continue;
                }
                $employee = Provider::find($row['employee_id']);
                if (!$employee) {
                    continue;
                }

                $material = Material::where('code', $row['mat_code'])->first();
                if (!$material) {
                    continue;
                }

                EmployeeMaterialLedger::create([
                    'request_id'      => $row['request_id'],
                    'issued_item_id'  => $row['issued_item_id'],
                    'indent_no'       => $row['indent_no'] ?? null,

                    'employee_id'     => $employee->id,
                    'state_id'        => $employee->state_id,
                    'district_id'     => $employee->district_id,


                     'material_id'     => $material->id,
                     'material_code'   => $row['mat_code'],

                    'has_serial'      => $row['mat_serial_no'] === 'Yes' ? 1 : 0,
                    'serial_number'   => $row['serial_no'],

                    'transaction_type'=> 'ISSUE',
                    'quantity'        => $row['issue_qty'],

                    'issue_date'      => $row['issue_date'],
                ]);
            }

            $this->info('Ledger sync completed');
            Log::info('Ledger sync completed');

        } catch (RequestException $e) {
            Log::error('Ledger sync failed', [
                'error' => $e->getMessage()
            ]);
        }

      
    }

  
}
