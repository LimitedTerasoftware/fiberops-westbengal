<?php

namespace App\Services;

use GuzzleHttp\Client;

class GisService
{
    protected $client;
    protected $baseUrl = 'http://54.179.22.74:8080/gisinterface/';
    protected $username = 'itisnoc';
    protected $password = 'Pwd#2@itisnoc';

    public function __construct()
    {
        $this->client = new Client(['base_uri' => $this->baseUrl]);
    }

    // Login with username and password
    public function login($username, $password)
    {
        $response = $this->client->get('user/login', [
            'query' => [
                'user' => $username,
                'pswd' => $password
            ]
        ]);
        return json_decode($response->getBody(), true);
    }

    // Logout with session key
    public function logout($sessionKey)
    {
        $response = $this->client->get('user/logout', [
            'query' => ['sessionKey' => $sessionKey]
        ]);
        return json_decode($response->getBody(), true);
    }

    // Get OLT Status, optional oltcode parameter
    public function getOltStatus($sessionKey, $oltcode = null)
    {
        $query = ['sessionKey' => $sessionKey];
        if ($oltcode) {
            $query['oltcode'] = $oltcode;
        }
        $response = $this->client->get('gis/getOltStatus', ['query' => $query]);
        return json_decode($response->getBody(), true);
    }

    // Get ONT Status, optional oltcode and ontcode parameters
    public function getOntStatus($sessionKey, $oltcode = null, $ontcode = null)
    {
        $query = ['sessionKey' => $sessionKey];
        if ($oltcode) {
            $query['oltcode'] = $oltcode;
        }
        if ($ontcode) {
            $query['ontcode'] = $ontcode;
        }
        $response = $this->client->get('gis/getOntStatus', ['query' => $query]);
        return json_decode($response->getBody(), true);
    }
}
