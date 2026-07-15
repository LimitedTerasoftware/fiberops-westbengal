<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;

class BiometricSync extends Command
{
    protected $signature = 'ledger:sync-biometric';

    protected $description = 'Sync employee biometric data';

   protected $endpoint = 'https://hr.terasoftware.com/admin/biometric_sync/auto_sync';

    public function handle()
    {
        $client = new Client();
        $startedAt = Carbon::now();

        try {
            $response = $client->get($this->endpoint, [
                'timeout' => 30,
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($statusCode === 200) {
                Log::info('Biometric sync succeeded', [
                    'status_code' => $statusCode,
                    'started_at'  => $startedAt->toDateTimeString(),
                    'duration_s'  => $startedAt->diffInSeconds(Carbon::now()),
                    'response'    => $body,
                ]);

                $this->info('Biometric sync completed successfully.');
            } else {
                Log::warning('Biometric sync returned a non-200 status', [
                    'status_code' => $statusCode,
                    'response'    => $body,
                ]);

                $this->warn("Biometric sync returned status {$statusCode}.");
            }
        } catch (RequestException $e) {
            $errorBody = $e->hasResponse()
                ? (string) $e->getResponse()->getBody()
                : null;

            Log::error('Biometric sync failed', [
                'message'  => $e->getMessage(),
                'response' => $errorBody,
            ]);

            $this->error('Biometric sync failed: ' . $e->getMessage());

            return 1;
        } catch (\Throwable $e) {
            Log::error('Biometric sync failed with an unexpected error', [
                'message' => $e->getMessage(),
            ]);

            $this->error('Biometric sync failed unexpectedly: ' . $e->getMessage());

            return 1;
        }

        return 0;
    }
}