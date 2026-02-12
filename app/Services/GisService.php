<?php

namespace App\Services;

use GuzzleHttp\Client;

class GisService
{
    private $baseUrl = "http://59.179.22.74:8080/snocinterface";
    private $loginUser = "itisnoc";
    private $loginPass = "Pwd#2@itisnoc";
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 30.0,
        ]);
    }

    // Generate session key (Login API internally)
    public function getSessionKey()
    {
        $loginUrl = "/snocinterface/user/login?user=" . urlencode($this->loginUser) . "&pswd=" . urlencode($this->loginPass);
        $response = $this->client->get($loginUrl);

        $body = json_decode($response->getBody(), true);
        \Log::info('Login API raw response:', $body);
        return $body['sessionKey'] ?? null;
    }

      public function getSessionKeyOlt()
    {
        $loginUrl = "http://59.179.22.74:8080/gisinterface/user/login?user=" . urlencode($this->loginUser) . "&pswd=" . urlencode($this->loginPass);
        $response = $this->client->get($loginUrl);

        $body = json_decode($response->getBody(), true);
        \Log::info('Login API raw response:', $body);
        return $body['sessionKey'] ?? null;
    }



    public function getOltStatus($oltcode = null)
    {
        $sessionKey = $this->getSessionKey();
        if (!$sessionKey) {
            return [
                "reqstatus" => "FAILURE",
                "remarks" => "Could not generate session key"
            ];
        }

        $url = "/snocinterface/api/getOltStatusInfo?sessionKey={$sessionKey}";
        if ($oltcode) {
            $url .= "&oltcode=" . urlencode($oltcode);
        }

         $response = $this->client->get($url);

        $body = json_decode($response->getBody(), true);
        return $body;
    }

     public function getOntStatus($oltcode = null, $ontcode = null)
    {
        $sessionKey = $this->getSessionKey();
        if (!$sessionKey) {
            return [
                "reqstatus" => "FAILURE",
                "remarks" => "Could not generate session key"
            ];
        }

        $url = "/snocinterface/api/getOntStatusInfo?sessionKey={$sessionKey}";
        if ($oltcode) {
            $url .= "&oltcode=" . urlencode($oltcode);
        }
        if ($ontcode) {
            $url .= "&ontcode=" . urlencode($ontcode);
        }

        $response = $this->client->get($url);
        $body = json_decode($response->getBody(), true);
        return $body;
    }


    // Get BharatNet NE Status
    public function getBharatNetNeStatus($stateName, $stateCode)
    {
        $sessionKey = $this->getSessionKey();
        //print_r($sessionKey);
        if (!$sessionKey) {
            return [
                "reqstatus" => "FAILURE",
                "remarks" => "Could not generate session key"
            ];
        }

        $url = "/gisinterface/gis/getBharatNetNeStatus?stateName={$stateName}&stateCode={$stateCode}&sessionKey={$sessionKey}";
        $response = $this->client->get($url);

        $body = json_decode($response->getBody(), true);
        return $body;
    }
}
