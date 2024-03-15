<?php namespace App\Service;

use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

class DomContentFramerService
{

    const NODE_XPATH_INJECT_ATTRIBUTE = "data-node-xpath";

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
                    $node->setAttribute(self::NODE_XPATH_INJECT_ATTRIBUTE, $this->prepareNodeXPath($node));
                }
            }
        });

        return $myCrawler->html();
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