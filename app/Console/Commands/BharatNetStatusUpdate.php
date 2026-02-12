<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


class BharatNetStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bharatnet:update';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call the BharatNet NE status API to insert/update tickets';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

public function handle()
{
    $url = 'https://westbengal.terasoftware.com/api/user/ne-status/insert';


    try {
        $client = new Client();
        $response = $client->post($url); 

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        if ($statusCode >= 200 && $statusCode < 300) {
            $this->info('BharatNet status updated successfully.');
            $this->info($body);

           

        } else {
            $this->error('API call failed. Status: ' . $statusCode);
            $this->error($body);

            Log::error("BharatNet Cron: API failed", [
                'status' => $statusCode,
                'response' => $body
            ]);
        }

    } catch (\Exception $e) {
        $this->error('Error calling API: ' . $e->getMessage());

        Log::error("BharatNet Cron: Exception occurred", [
            'error' => $e->getMessage()
        ]);
    }


    return 0;
}

}
