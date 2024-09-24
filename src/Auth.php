<?php
namespace App;

class Auth
{
    private $apiKey;

    public function __construct()
    {
        // Ideally, store API keys securely, e.g., in environment variables
        $this->apiKey = '';
    }

    public function isAuthenticated()
    {
        $providedKey = $_GET['api_key'] ?? '';

        return hash_equals($this->apiKey, $providedKey);
    }
}
