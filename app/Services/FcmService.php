<?php
namespace App\Services;

use GuzzleHttp\Client;

class FcmService
{
    protected $client;
    protected $projectId;
    protected $clientEmail;
    protected $privateKey;

    public function __construct()
    {
        $this->client      = new Client();
        $this->projectId   = env('FIREBASE_PROJECT_ID');
        $this->clientEmail = env('FIREBASE_CLIENT_EMAIL');
        $this->privateKey  = str_replace('\\n', "\n", env('FIREBASE_PRIVATE_KEY'));
    }

    private function getAccessToken()
    {
        $now = time();

        $header = rtrim(strtr(base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ])), '+/', '-_'), '=');

        $payload = rtrim(strtr(base64_encode(json_encode([
            'iss'   => $this->clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ])), '+/', '-_'), '=');

        $unsignedJwt = $header . '.' . $payload;
        openssl_sign($unsignedJwt, $signature, $this->privateKey, 'SHA256');
        $jwt = $unsignedJwt . '.' . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        $response = $this->client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        return $body['access_token'];
    }

   public function sendToUser($fcmToken, $title, $body, array $data = [])
{
    $accessToken = $this->getAccessToken();
    $url = 'https://fcm.googleapis.com/v1/projects/' . $this->projectId . '/messages:send';

    $message = [
        'token'        => $fcmToken,
        'notification' => [
            'title' => $title,
            'body'  => $body,
        ],
    ];

    if (!empty($data)) {
        $stringData = [];
        foreach ($data as $key => $value) {
            $stringData[(string)$key] = (string)$value; 
        }
        $message['data'] = $stringData;
    }

    $response = $this->client->post($url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ],
        'json' => [
            'message' => $message,
        ],
    ]);

    return json_decode($response->getBody(), true);
}
public function sendData($fcmToken, array $data = [])
{
    $accessToken = $this->getAccessToken();
    $url = 'https://fcm.googleapis.com/v1/projects/' . $this->projectId . '/messages:send';

    // Cast all keys and values to string
    $stringData = [];
    foreach ($data as $key => $value) {
        $stringData[(string)$key] = (string)$value;
    }

    $response = $this->client->post($url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ],
        'json' => [
            'message' => [
                'token' => $fcmToken,
                'data'  => $stringData,  
            ],
        ],
    ]);

    return json_decode($response->getBody(), true);
}

}