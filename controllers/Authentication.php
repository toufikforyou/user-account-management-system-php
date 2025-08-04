<?php

namespace Controllers;

class Authentication
{
    private $apiKey;
    private $apiToken;

    public function __construct(?string $apiKey, ?string $apiToken)
    {
        $this->apiKey = $apiKey;
        $this->apiToken = $apiToken;
    }

    public function verifyToken(): bool
    {
        if(!$this->apiKey && !$this->apiToken)
        {
            return false;
        }
        
        // Check if the API key and API token are valid
        if (!$this->isValidApiKeyAndToken()) {
            return false;
        }
        return true;
    }

    private function isValidApiKeyAndToken(): bool
    {
        // Check if the API key and API token exist in the database or configuration file
        $validApiKeysAndTokens = [
            'api_key_1' => 'api_token_1',
            'api_key_2' => 'api_token_2',
            'api_key_3' => 'api_token_3'
        ];
        
        return isset($validApiKeysAndTokens[$this->apiKey]) && $validApiKeysAndTokens[$this->apiKey] === $this->apiToken;
    }


}