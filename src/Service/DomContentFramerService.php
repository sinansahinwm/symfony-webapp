<?php namespace App\Service;

use App\Service\CrawlerExtractor\PageLinksCrawlerExtractor;
use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

class DomContentFramerService
{

    const NODE_XPATH_INJECT_ATTRIBUTE = "data-node-xpath";
    const CRAWLER_EXTRACTOR_CLASS_POSTFIX = "CrawlerExtractor";

    private string $html;
    private ?string $urlSchemeSource = NULL;

    public function getHtml(): string
    {
        return $this->html;
    }

    public function setHtml(string $html): self
    {
        $this->html = $html;
        return $this;
    }

    public function getUrlSchemeSource(): ?string
    {
        return $this->urlSchemeSource;
    }

    public function setUrlSchemeSource(?string $urlSchemeSource): self
    {
        $this->urlSchemeSource = $urlSchemeSource;
        return $this;
    }


    public function getFramedContent(): string
    {
        $myCrawler = new Crawler($this->getHtml());

        // Remove Script Nodes
        $myCrawler->filterXPath('//script')->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        // Remove Inline Styles
        $myCrawler->filterXPath('//style')->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        // Convert Stylesheets To Absolute URL
        $myCrawler->filterXPath('//link[@rel="stylesheet"]')->each(function (Crawler $crawler) {
            $stylesheetHref = $crawler->attr('href');
            if ($this->canBePrefixed($stylesheetHref) === TRUE) {
                foreach ($crawler as $node) {
                    $node->setAttribute("href", $this->convertUrlToAbsoluteUrl($stylesheetHref));
                }
            }
        });

        // Convert Images To Absolute URL
        $myCrawler->filterXPath('//img[@src]')->each(function (Crawler $crawler) {
            $imageSource = $crawler->attr('src');
            if ($this->canBePrefixed($imageSource) === TRUE) {
                foreach ($crawler as $node) {
                    $node->setAttribute("src", $this->convertUrlToAbsoluteUrl($imageSource));
                }
            }
        });

        // Convert Links To Absolute URL
        $myCrawler->filterXPath('//a[@href]')->each(function (Crawler $crawler) {
            $aLink = $crawler->attr('href');
            if ($this->canBePrefixed($aLink) === TRUE) {
                foreach ($crawler as $node) {
                    $node->setAttribute("href", $this->convertUrlToAbsoluteUrl($aLink));
                }
            }
        });

        // Set Area Highlighters
        $myCrawler->filterXPath('//*')->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                if ($node->getNodePath() !== NULL) {

                    // Set Path Attribute
                    $node->setAttribute(self::NODE_XPATH_INJECT_ATTRIBUTE, $this->prepareNodeXPath($node));

                    // Add Class Attributes
                    $nodeClasses = [$node->getAttribute("class"), "nodeXPath"];
                    $node->setAttribute("class", implode(" ", $nodeClasses));

                }
            }
        });

        return $myCrawler->html();
    }

    public function extractData(): array
    {
        // Get Crawler
        $myCrawler = new Crawler($this->getHtml());
        $extractedData = [];

        // Get Crawler Extractor Classes
        $extractorClasses = $this->getExtractorClasses();

        // Loop All Extractors
        foreach ($extractorClasses as $extractorClass) {
            // Run Extractor
            $extractedData[] = [
                "name" => $extractorClass::name(),
                "data" => $extractorClass::extract($myCrawler),
            ];
        }

        return $extractedData;
    }

    private function getExtractorClasses(): array
    {
        return [
            PageLinksCrawlerExtractor::class,
        ];
    }

    private function canBePrefixed(string $rawUrl): bool
    {
        $parsedHost = parse_url($rawUrl, PHP_URL_HOST);
        $parsedPath = parse_url($rawUrl, PHP_URL_PATH);
        return ($parsedHost === NULL) && ($parsedPath !== NULL);
    }

    private function convertUrlToAbsoluteUrl(string $initialPath): string
    {
        if ($this->urlSchemeSource === NULL) {
            return $initialPath;
        }
        $absoluteOriginParts = [
            parse_url($this->urlSchemeSource, PHP_URL_SCHEME),
            '://',
            parse_url($this->urlSchemeSource, PHP_URL_HOST),
            str_starts_with($initialPath, '/') ? $initialPath : '/' . $initialPath
        ];
        return implode('', $absoluteOriginParts);
    }

    private function prepareNodeXPath(DomNode $node): string
    {
        $nodeHasChildNodes = $node->hasChildNodes();
        $nodeHasAttributes = $node->hasAttributes();
        if ($nodeHasChildNodes === TRUE) {
            return $node->getNodePath();
        }

        return "";
    }


}