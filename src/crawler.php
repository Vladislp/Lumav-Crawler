<?php
namespace App;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Crawler
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10.0,
            'headers' => [
                'User-Agent' => 'EcommerceCrawler/1.0',
            ],
        ]);
    }

    public function parsePage($html)
    {
        $crawler = new DomCrawler($html);
        return $crawler;
    }

    public function extractCategories(DomCrawler $crawler)
    {
        $categories = $crawler->filter('li.vl-flyout-nav__js-tab')->each(function (DomCrawler $node) {
            // Get the main category name
            $categoryName = trim($node->filter('a')->text());
            // Get the main category URL
            $categoryUrl = trim($node->filter('a')->attr('href'));
    
            // Initialize subcategories array
            $subCategories = [];
    
            // Check if there are any subcategories present
            $subCategoryNodes = $node->filter('.vl-flyout-nav__flyout .vl-flyout-nav__sub-cats nav ul li a');
            $subCategoryNodes->each(function (DomCrawler $subNode) use (&$subCategories) {
                $subCategoryName = trim($subNode->text());
                $subCategoryUrl = trim($subNode->attr('href'));
    
                // Add to the subcategories array
                $subCategories[] = [
                    'name' => $subCategoryName,
                    'url' => $subCategoryUrl,
                ];
            });
    
            return [
                'name' => $categoryName,
                'url' => $categoryUrl,
                'subcategories' => $subCategories,
            ];
        });
    
        return $categories;
    }

    public function extractToolbar(DomCrawler $crawler)
{
    $toolbarItems = $crawler->filter('#gh-top ul#gh-topl li')->each(function (DomCrawler $node) {
        $linkText = trim($node->filter('a')->text());
        $linkUrl = trim($node->filter('a')->attr('href'));

        return [
            'text' => $linkText,
            'url' => $linkUrl,
        ];
    });

    return $toolbarItems;
}
    
    

    public function extractProducts(DomCrawler $crawler)
    {
        // Example selector, adjust based on the actual HTML structure of product items
        $products = $crawler->filter('.item-class-selector')->each(function (DomCrawler $node) {
            $name = $node->filter('.product-name-class-selector')->text(); // Adjust selector
            $price = $node->filter('.product-price-class-selector')->text(); // Adjust selector
            $category = $node->filter('.product-category-class-selector')->text(); // Adjust as needed

            return [
                'name' => trim($name),
                'price' => trim($price),
                'category' => trim($category),
            ];
        });

        return $products;
    }

// Implement additional extraction methods as needed

    public function fetchPage($url)
    {
        try {
            $response = $this->client->request('GET', $url, [
                'verify' => false, // Disable SSL verification (not recommended for production)
            ]);

            return (string) $response->getBody();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Log the request exception (e.g., 404, 500 errors)
            error_log("Request failed: " . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            // Log general exceptions
            error_log("An error occurred: " . $e->getMessage());
            return null;
        }
    }

}
