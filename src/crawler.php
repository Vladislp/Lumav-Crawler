<?php
namespace App;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Crawler
{
    private $client;

    // Constructor method to initialize the HTTP client
    public function __construct()
    {
        // Create a new Guzzle client with a 10-second timeout and custom headers
        $this->client = new Client([
            'timeout' => 10.0,
            'headers' => [
                'User-Agent' => 'EcommerceCrawler/1.0',
            ],
        ]);
    }

    // Method to fetch the HTML content of a given URL
    public function fetchPage(string $url): ?string
    {
        try {
            $response = $this->client->request('GET', $url, [
                'verify' => false,
            ]);
            return (string) $response->getBody();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            error_log("Request failed: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("An error occurred: " . $e->getMessage());
        }
        return null;
    }

    // Method to parse the HTML content into a DomCrawler instance
    public function parsePage(string $html): DomCrawler
    {
        return new DomCrawler($html);
    }

    // Method to extract banner information from eBay
    public function extractBannerInfo(DomCrawler $crawler): array
    {
        return $crawler->filter('.vl-banner-color-block__content')->each(function (DomCrawler $node) {
            return [
                'heading' => trim($node->filter('.vl-banner__text-heading span')->text()),
                'subheading' => trim($node->filter('.vl-banner__text-subheading span')->text()),
                'cta_url' => trim($node->filter('.vl-cta a')->attr('href')),
                'cta_text' => trim($node->filter('.vl-cta__text-only')->text()),
            ];
        });
    }

    // Method to extract categories from AliExpress
    public function extractCategoriesFromAliExpress(DomCrawler $crawler): array
    {
        return $crawler->filter('.pc2023-header-tab--tab-item--2Hs_3sA')->each(function (DomCrawler $node) {
            return [
                'name' => trim($node->text()),
                'url' => trim($node->attr('href')),
            ];
        });
    }

    // Method to extract products from AliExpress
    public function extractProductsFromAliExpress(DomCrawler $crawler): array
    {
        return $crawler->filter('.new-user--itemWrap--23_AGec')->each(function (DomCrawler $node) {
            return [
                'link' => trim($node->attr('href')),
                'image_url' => trim($node->filter('img')->attr('src')),
                'discount' => $node->filter('.new-user--discount--2ptxNmf')->count() > 0 
                    ? trim($node->filter('.new-user--discount--2ptxNmf')->text()) 
                    : 'No discount',
                'min_price' => trim($node->filter('.new-user--minPrice--27O2j3o')->text()),
                'origin_price' => trim($node->filter('.new-user--originPrice--2y3Jdz7')->text()),
            ];
        });
    }

    // Method to extract categories from eBay
    public function extractCategories(DomCrawler $crawler): array
    {
        return $crawler->filter('li.vl-flyout-nav__js-tab')->each(function (DomCrawler $node) {
            $subCategories = $node->filter('.vl-flyout-nav__flyout .vl-flyout-nav__sub-cats nav ul li a')->each(function (DomCrawler $subNode) {
                return [
                    'name' => trim($subNode->text()),
                    'url' => trim($subNode->attr('href')),
                ];
            });

            return [
                'name' => trim($node->filter('a')->text()),
                'url' => trim($node->filter('a')->attr('href')),
                'subcategories' => $subCategories,
            ];
        });
    }

    // Method to extract products from eBay
    public function extractProducts(DomCrawler $crawler): array
    {
        return $crawler->filter('.s-item')->each(function (DomCrawler $node) {
            return [
                'name' => trim($node->filter('.s-item__title')->text()),
                'price' => trim($node->filter('.s-item__price')->text()),
                'category' => '', // eBay may not provide the category directly
            ];
        });
    }
}
