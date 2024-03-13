<?php namespace App\Service\NodeApp;

use App\Message\NodeAppPackageDeliveryMessage;
use App\Service\NodeApp\PuppeteerReplayer\PuppeteerReplayCombinator;
use App\Service\NodeApp\PuppeteerReplayer\PuppeteerReplayLoader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Messenger\MessageBusInterface;

class PuppeteerReplayerNodeApp implements NodeAppInterface
{

    private ?string $recordPath = NULL;
    private ?string $webHookUrl = NULL;
    private ?string $instanceID = NULL;
    private int $timeOut;
    private array $puppeteerLaunchOptions;

    public function __construct(private PuppeteerReplayLoader $puppeteerReplayLoader, private PuppeteerReplayCombinator $puppeteerReplayCombinator)
    {
    }

    public function setRecordPath(string $recordPath): self
    {
        $this->recordPath = $recordPath;
        return $this;
    }

    public function setOptions(string $webHookUrl, string $instanceID, int $timeOut = 700, array $puppeteerLaunchOptions = []): self
    {
        $this->webHookUrl = $webHookUrl;
        $this->instanceID = $instanceID;
        $this->timeOut = $timeOut;
        $this->puppeteerLaunchOptions = $puppeteerLaunchOptions;
        return $this;
    }

    public function getName(): string
    {
        return "puppeteer_replayer";
    }

    public function getEntrypointContent(): string
    {
        if ($this->recordPath === NULL or $this->webHookUrl === NULL or $this->instanceID === NULL) {
            throw new FileNotFoundException();
        }
        $myLoader = $this->puppeteerReplayLoader->loadRecord($this->recordPath);
        $combinedContent = $this->puppeteerReplayCombinator->setLoader($myLoader)->combineWith($this->webHookUrl, $this->instanceID, $this->timeOut, $this->puppeteerLaunchOptions);
        return $combinedContent;
    }

}