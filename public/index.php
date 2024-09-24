<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Api;

// Enable CORS if needed
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Instantiate and handle the API request
$api = new Api();
$api->handleRequest();
