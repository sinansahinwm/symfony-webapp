<?php namespace App\Service;

use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

class DomContentFramerService
{
    const NODE_XPATH_INJECT_ATTRIBUTE = 'node-xpath';
    private ?string $html = NULL;
    private ?string $baseURL = NULL;

    public function getHtml(): string
    {
        return $this->html;
    }

    public function setHtml(string $html, bool $isBase64Decoded = TRUE): self
    {
        if ($isBase64Decoded === TRUE) {
            $this->html = base64_decode($html);
        } else {
            $this->html = $html;
        }
        return $this;
    }

    public function getBaseURL(): ?string
    {
        return $this->baseURL;
    }

    public function setBaseURL(?string $baseURL): self
    {
        $parsedScheme = parse_url($baseURL, PHP_URL_SCHEME);
        $parsedHost = parse_url($baseURL, PHP_URL_HOST);
        $parsedPort = parse_url($baseURL, PHP_URL_PORT);
        $parsedBaseURL = $parsedScheme . "://" . $parsedHost . (($parsedPort !== NULL) ? ':' . $parsedPort : '');
        $this->baseURL = $parsedBaseURL;
        return $this;
    }

    public function getFramedContent($clearScripts = TRUE, $clearStyles = TRUE, $addNodeXPathAttributes = TRUE): string
    {

        // Create Crawler
        $myCrawler = new Crawler($this->getHtml());

        // Remove Script Nodes
        if ($clearScripts === TRUE) {
            $myCrawler->filterXPath('//script')->each(function (Crawler $crawler) {
                foreach ($crawler as $node) {
                    $node->parentNode->removeChild($node);
                }
            });
        }


        // Remove Inline Styles
        if ($clearStyles === TRUE) {
            $myCrawler->filterXPath('//style')->each(function (Crawler $crawler) {
                foreach ($crawler as $node) {
                    $node->parentNode->removeChild($node);
                }
            });
        }

        // Convert Stylesheets To Absolute URL
        $myCrawler->filterXPath('//link[@rel="stylesheet"]')->each(function (Crawler $crawler) {
            $stylesheetHref = $crawler->attr('href');

            if ($this->needsPrefixing($stylesheetHref) === TRUE) {
                foreach ($crawler as $node) {
                    $convertedAbsoluteURL = $this->convertToAbsoluteURL($stylesheetHref);
                    $node->setAttribute("href", $convertedAbsoluteURL);
                }
            }
        });

        // Convert Images To Absolute URL
        $myCrawler->filterXPath('//img[@src]')->each(function (Crawler $crawler) {
            $imageSource = $crawler->attr('src');
            if ($this->needsPrefixing($imageSource) === TRUE) {
                foreach ($crawler as $node) {
                    $node->setAttribute("src", $this->convertToAbsoluteURL($imageSource));
                }
            }
        });

        // Convert Links To Absolute URL
        $myCrawler->filterXPath('//a[@href]')->each(function (Crawler $crawler) {
            $aLink = $crawler->attr('href');
            if ($this->needsPrefixing($aLink) === TRUE) {
                foreach ($crawler as $node) {
                    $node->setAttribute("href", $this->convertToAbsoluteURL($aLink));
                }
            }
        });

        // Set Area Highlighters
        if ($addNodeXPathAttributes === TRUE) {
            $myCrawler->filterXPath('//*')->each(function (Crawler $crawler) {
                foreach ($crawler as $node) {
                    if ($node->getNodePath() !== NULL) {
                        if ($node instanceof DOMNode) {

                            // Get Node XPath String
                            $nodeXPath = $this->getNodeXPathStr($node);

                            // Set Path Attribute
                            if ($nodeXPath !== NULL) {
                                $node->setAttribute(self::NODE_XPATH_INJECT_ATTRIBUTE, $nodeXPath);
                            }

                        }
                    }
                }
            });
        }
        return $myCrawler->html();

    }

    private function needsPrefixing(?string $rawUrl = NULL): bool
    {
        // Check CDN URL
        $parsedScheme = parse_url($rawUrl, PHP_URL_SCHEME);
        $parsedHost = parse_url($rawUrl, PHP_URL_HOST);
        $parsedPath = parse_url($rawUrl, PHP_URL_PATH);
        $parsedPort = parse_url($rawUrl, PHP_URL_PORT);

        // Check Needs Prefixing
        if (str_starts_with($rawUrl, '//') === FALSE && $parsedHost === NULL && $rawUrl !== NULL && $parsedScheme === NULL && $parsedPort === NULL && $parsedPath !== NULL && $rawUrl !== '/') {
            return TRUE;
        }

        return FALSE;
    }

    private function convertToAbsoluteURL(string $initialPath): string
    {
        $myBaseURL = $this->getBaseURLWithoutTrailingSlashes();
        return str_starts_with($initialPath, '/') ? $myBaseURL . $initialPath : $myBaseURL . "/" . $initialPath;
    }

    private function getBaseURLWithoutTrailingSlashes(): string
    {
        if (str_ends_with($this->getBaseURL(), '/')) {
            return rtrim(rtrim($this->getBaseURL(), "/"));
        }
        return $this->getBaseURL();
    }

    private function getNodeXPathStr(DOMNode $node): string|null
    {
        return $node->getNodePath();
    }


}