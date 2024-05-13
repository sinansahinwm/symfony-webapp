<?php namespace App\Service\CrawlerExtractor;

use Symfony\Component\DomCrawler\Crawler;

class PageLinksCrawlerExtractor
{
    const FILTER_XPATH = "//a[@href]";

    public static function name(): string
    {
        return "Sayfadaki Linkler";
    }

    public static function extract(Crawler $crawler): array
    {
        $filteredData = $crawler->filterXPath(self::FILTER_XPATH)->each(function (Crawler $crawler) {
            return [
                "text" => $crawler->innerText(),
                "url" => $crawler->attr('href')
            ];
        });

        return array_filter($filteredData, function ($data) {
            return $data !== NULL;
        });
    }
}