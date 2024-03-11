<?php namespace App\Service\ChromeRecordPlayer;

use Exception;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Exception\UnexpectedValueException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
class ChromeRecordPlayer
{

    const STEP_SETVIEWPORT = "SETVIEWPORT";
    const STEP_NAVIGATE = "NAVIGATE";
    const STEP_CHANGE = "CHANGE";
    const STEP_CLICK = "CLICK";

    private array $recordSteps = [];
    private array $formFields = [];

    private ?string $recordTitle = NULL;
    private AbstractBrowser $myBrowser;
    private Crawler $myCrawler;

    public function __construct(private ChromeRecordReader $chromeRecordReader)
    {
    }

    public function load(string $filePath): self
    {
        $this->chromeRecordReader->loadRecord($filePath);
        $this->recordSteps = $this->chromeRecordReader->getRecordSteps();
        $this->recordTitle = $this->chromeRecordReader->getRecordTitle();
        $this->myBrowser = $this->chromeRecordReader->getBrowser();
        return $this;
    }

    public function play(): Crawler
    {

        // Check Record File Is Loaded
        if ($this->chromeRecordReader->isSuccess() !== TRUE) {
            throw new FileNotFoundException();
        }

        // Play Steps
        foreach ($this->recordSteps as $recordStep) {
            $this->playStep($recordStep);
        }

        return $this->myCrawler;
    }

    private function playStep($recordStep): void
    {
        $stepType = strtoupper($recordStep->type);

        // TODO: Set Viewport
        if ($stepType === self::STEP_SETVIEWPORT) {

        }

        // Navigate Step
        if ($stepType === self::STEP_NAVIGATE) {
            $this->myCrawler = $this->myBrowser->request('GET', $recordStep->url);
            $this->checkAssertions($recordStep->assertedEvents ?? NULL);
        }

        // Form Input
        if ($stepType === self::STEP_CHANGE) {
            $flattenSelector = $this->getFlattenXPathSelector($recordStep->selectors, 'input');
            if ($flattenSelector !== NULL) {
                $filteredNode = $this->myCrawler->filterXPath($flattenSelector);
                $nodeIdentity = $filteredNode->attr('name');
                $this->formFields[$nodeIdentity] = $recordStep->value;
            }
        }

        if ($stepType === self::STEP_CLICK) {
            $flattenSelector = $this->getFlattenXPathSelector($recordStep->selectors, 'button');
            if ($flattenSelector !== NULL) {
                $filteredNode = $this->myCrawler->filterXPath($flattenSelector);
                $thisButtonForm = $filteredNode->closest("form");
                $thisFormButtonCount = $thisButtonForm->children("button")->count();
                $buttonText = $filteredNode->text();
                if ($thisFormButtonCount === 1) {
                    $this->myCrawler = $this->myBrowser->submitForm($buttonText, $this->formFields);
                    $this->formFields = [];
                }
            }
        }

        $this->checkAssertions($recordStep);

    }


    private function getFlattenXPathSelector(array $recordSelectors, ?string $expectedBehavior = NULL): string|null
    {
        foreach ($recordSelectors as $selector) {
            foreach ($selector as $rawSelector) {
                $clearRawSelector = $this->clearFixChromeSelectorPrefix($rawSelector, $expectedBehavior);
                if ($clearRawSelector !== NULL) {
                    try {
                        $filteredNode = $this->myCrawler->filterXPath($clearRawSelector);
                        $nodeName = $filteredNode->nodeName();
                        if ($expectedBehavior === NULL) {
                            return $clearRawSelector;
                        }
                        if (strtolower($expectedBehavior) === strtolower($nodeName)) {
                            return $clearRawSelector;
                        }
                    } catch (Exception) {
                        continue;
                    }
                }
            }
        }
        return NULL;
    }

    private function clearFixChromeSelectorPrefix(string $rawSelector, ?string $expectedBehavior = NULL): ?string
    {
        if (str_starts_with($rawSelector, 'xpath')) {
            $rawSelector = str_replace('xpath/', '', $rawSelector);
        } else if (str_starts_with($rawSelector, 'pierce')) {
            $rawSelector = str_replace('pierce/', '', $rawSelector);
            if ($expectedBehavior !== NULL) {
                if (str_starts_with($rawSelector, 'pierce/#')) {
                    $pierceWithItemID = str_replace('pierce/#', '', $rawSelector);
                    exit("TODOO");
                }
            }
        } else if (str_starts_with($rawSelector, 'aria')) {
            $rawSelector = str_replace('aria/', '', $rawSelector);
        } else if (str_starts_with($rawSelector, 'text')) {
            $rawSelector = str_replace('text/', '', $rawSelector);
            if ($expectedBehavior !== NULL) {
                $rawSelector = '//' . $expectedBehavior . '[contains(text(),"' . $rawSelector . '")]';
            }
        }
        return $rawSelector;
    }

    private function checkAssertions($step): void
    {
        $assertedEvents = $step->assertedEvents ?? NULL;
        if ($assertedEvents !== NULL) {
            foreach ($assertedEvents as $assertedEvent) {
                if ($assertedEvent->type === "navigation") {
                    $navAssertTitle = $assertedEvent->title;
                    $thePageTitle = $this->myCrawler->filterXPath("//title[1]")->innerText();
                    if ($navAssertTitle !== $thePageTitle) {
                        throw new UnexpectedValueException();
                    }
                }
                // TODO : Other assert types
            }
        }
    }

}