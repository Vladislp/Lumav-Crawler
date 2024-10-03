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
            // Initialize the Crawler object to handle page requests and parsing
            $crawler = new Crawler();

            // Read URLs from the text file
            $urls = file(__DIR__ . '/../urls.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            if (empty($urls)) {
                throw new \Exception('No URLs found to crawl.');
            }

            $results = [];
            // Loop through each URL and process it
            foreach ($urls as $url) {
                // Fetch the HTML content of the page
                $html = $crawler->fetchPage($url);
                if ($html) {
                    // Parse the HTML content using DomCrawler
                    $domCrawler = $crawler->parsePage($html);
                    // Extract categories, products, and banner info from the parsed page
                    $categories = $crawler->extractCategories($domCrawler);
                    $products = $crawler->extractProducts($domCrawler);
                    $bannerinfo = $crawler->extractBannerInfo($domCrawler);

                    // Check if the URL is for AliExpress
                    if (strpos($url, 'aliexpress.com') !== false) {
                        $categories_ali = $crawler->extractCategoriesFromAliExpress($domCrawler);
                        $products_ali = $crawler->extractProductsFromAliExpress($domCrawler);

                        // Only include AliExpress-specific data for AliExpress URLs
                        $results[] = [
                            'url' => $url,
                            'categories' => $categories,
                            'products' => $products,
                            'bannerInfo' => $bannerinfo,
                            'categories' => $categories_ali,
                            'products' => $products_ali,
                        ];
                    } else {
                        // For non-AliExpress URLs, don't include the AliExpress section
                        $results[] = [
                            'url' => $url,
                            'categories' => $categories,
                            'products' => $products,
                            'bannerInfo' => $bannerinfo,
                        ];
                    }
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
