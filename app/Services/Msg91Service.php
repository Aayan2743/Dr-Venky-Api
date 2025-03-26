<?php

namespace App\Services;

use GuzzleHttp\Client;

class Msg91Service
{
    protected $client;
    protected $apiKey;
    protected $senderId;
    protected $entityId;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.msg91.com/api/v5/']);
        $this->apiKey = env('MSG91_API_KEY');
        $this->senderId = env('MSG91_SENDER_ID');
        $this->entityId = env('MSG91_ENTITY_ID'); // DLT Entity ID
      
    }

    public function sendSms($mobileNumber, $templateId, $variables = [])
    {
        $endpoint = 'flow/';

        try {
            $response = $this->client->post($endpoint, [
                'headers' => [
                    'authkey' => $this->apiKey,
                ],
                'json' => [
                    'sender' => $this->senderId,
                    'flow_id' => $templateId, // Use the DLT Template ID
                    'recipients' => [
                        [
                            'mobiles' => $mobileNumber,
                            'variables' => $variables, // Pass variables to replace placeholders in the template
                        ],
                    ],
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
