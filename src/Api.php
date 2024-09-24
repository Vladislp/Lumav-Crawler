<?php
namespace App;

class Api
{
    private $auth;

    public function __construct()
    {
        $this->auth = new Auth();
    }

    public function handleRequest()
    {
        // Simple routing based on the 'action' query parameter
        $action = $_GET['action'] ?? '';

        // Debugging output
        $providedKey = $_GET['api_key'] ?? '';
        error_log("Provided API Key: " . $providedKey);

        // Authenticate the request
        if (!$this->auth->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        switch ($action) {
            case 'crawl':
                $this->handleCrawl();
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
        }
    }

    private function handleCrawl()
    {
        try {
            $crawler = new Crawler();

            // Read URLs from the text file
            $urls = file(__DIR__ . '/../urls.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            if (empty($urls)) {
                throw new \Exception('No URLs found to crawl.');
            }

            $results = [];

            foreach ($urls as $url) {
                $html = $crawler->fetchPage($url);
                if ($html) {
                    $domCrawler = $crawler->parsePage($html);
                    $categories = $crawler->extractCategories($domCrawler);
                    $products = $crawler->extractProducts($domCrawler);
                    $toolbar = $crawler->extractToolbar($domCrawler); 

                    $results[] = [
                        'url' => $url,
                        'categories' => $categories,
                        'products' => $products,
                        'toolbar' => $toolbar,
                    ];
                } else {
                    $results[] = [
                        'url' => $url,
                        'error' => 'Failed to fetch the page.',
                    ];
                }
            }

            echo json_encode(['status' => 'success', 'data' => $results]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
