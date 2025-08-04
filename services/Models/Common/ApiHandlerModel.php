<?php

namespace Services\Models\Common;

class ApiHandlerModel
{
    private string $api_key = 'api_key_1';
    private string $api_token = 'api_token_1';

    protected function apiResponseModel($method, $url, array $data): string
    {
        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set the request method
        switch ($method) {
            case "GET":
                // GET request doesn't have a request body
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            default:
                throw new \Exception("Invalid request method: " . $method);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-API-KEY: ' . $this->api_key,
            'X-API-TOKEN: ' . $this->api_token
        ));

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verify SSL certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // Check for SSL common name matching

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("cURL error: " . $error);
        }

        // Close cURL session
        curl_close($ch);

        return $response;
    }

}