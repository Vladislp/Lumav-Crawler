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

    public function extractBannerInfo(DomCrawler $crawler)
    {
        $bannerInfo = $crawler->filter('.vl-banner-color-block__content')->each(function (DomCrawler $node) {
            $heading = $node->filter('.vl-banner__text-heading span')->text();
            $subheading = $node->filter('.vl-banner__text-subheading span')->text();
            $ctaUrl = $node->filter('.vl-cta a')->attr('href');
            $ctaText = $node->filter('.vl-cta__text-only')->text();
    
            return [
                'heading' => trim($heading),
                'subheading' => trim($subheading),
                'cta_url' => trim($ctaUrl),
                'cta_text' => trim($ctaText),
            ];
        });
    
        // Convert the banner information array to JSON format
        return $bannerInfo;
    }

    public function extractCategoriesFromAliExpress(DomCrawler $crawler)
    {
        $categories = $crawler->filter('.pc2023-header-tab--tab-item--2Hs_3sA')->each(function (DomCrawler $node) {
            $categoryName = trim($node->text());
            $categoryUrl = trim($node->attr('href'));

            return [
                'name' => $categoryName,
                'url' => $categoryUrl,
            ];
        });

        return $categories;
    }

    public function extractProductsFromAliExpress(DomCrawler $crawler)
    {
        $products = $crawler->filter('.new-user--itemWrap--23_AGec')->each(function (DomCrawler $node) {
            $link = $node->attr('href');
            $imageUrl = $node->filter('img')->attr('src');
            $discount = $node->filter('.new-user--discount--2ptxNmf')->text();
            $minPrice = $node->filter('.new-user--minPrice--27O2j3o')->text();
            $originPrice = $node->filter('.new-user--originPrice--2y3Jdz7')->text();

            return [
                'link' => trim($link),
                'image_url' => trim($imageUrl),
                'discount' => trim($discount),
                'min_price' => trim($minPrice),
                'origin_price' => trim($originPrice),
            ];
        });

        return $products;
    }
    
    public function extractCategories(DomCrawler $crawler)
    {
        $categories = $crawler->filter('li.vl-flyout-nav__js-tab')->each(function (DomCrawler $node) {
            $categoryName = trim($node->filter('a')->text());
            $categoryUrl = trim($node->filter('a')->attr('href'));
    
            $subCategories = [];
    
            $subCategoryNodes = $node->filter('.vl-flyout-nav__flyout .vl-flyout-nav__sub-cats nav ul li a');
            $subCategoryNodes->each(function (DomCrawler $subNode) use (&$subCategories) {
                $subCategoryName = trim($subNode->text());
                $subCategoryUrl = trim($subNode->attr('href'));
    
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

    public function extractProducts(DomCrawler $crawler)
    {
        $products = $crawler->filter('.s-item')->each(function (DomCrawler $node) {
            $name = $node->filter('.s-item__title')->text(); // Adjust selector for product name
            $price = $node->filter('.s-item__price')->text(); // Adjust selector for product price
            $category = ''; // eBay may not provide the category directly
            
            return [
                'name' => trim($name),
                'price' => trim($price),
                'category' => trim($category),
            ];
        });

        return $products;
    }

    public function fetchPage($url)
    {
        try {
            $response = $this->client->request('GET', $url, [
                'verify' => false, // Disable SSL verification (not recommended for production)
            ]);

            return (string) $response->getBody();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            error_log("Request failed: " . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            error_log("An error occurred: " . $e->getMessage());
            return null;
        }
    }
}

