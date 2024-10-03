<?php
namespace App;

class Auth
{
    private $apiKey;
    // Constructor to initialize the API key
    public function __construct()
    {
        // Ideally, store API keys securely, e.g., in environment variables
        $this->apiKey = '12345';
    }
    // Method to authenticate API requests
    public function isAuthenticated()
    {
        // Retrieve the 'api_key' parameter from the GET request, if it exists. If not, use an empty string.
        $providedKey = $_GET['api_key'] ?? '';
        // Use a constant-time string comparison to check if the provided API key matches the stored key.
        // This method helps prevent timing attacks.
        return hash_equals($this->apiKey, $providedKey);
    }
}
