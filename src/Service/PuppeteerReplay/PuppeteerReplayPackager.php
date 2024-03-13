<?php namespace App\Service\PuppeteerReplay;

use App\Service\NodeAppPackagerService;

class PuppeteerReplayPackager
{

    const NODE_APP_NAME = 'puppeteer_replayer';
    const NODE_PACKAGE_START_ENTRYPOINT = 'index.js';
    const NODE_PACKAGE_GLOB = "/*.*";
    private PuppeteerReplayCombinator $replayCombinator;

    public function __construct(PuppeteerReplayCombinator $replayCombinator, private PuppeteerReplayLoader $replayLoader, private NodeAppPackagerService $nodeAppPackagerService)
    {
        $this->replayCombinator = $replayCombinator;
    }

    public function loadRecord(string $recordPath): self
    {
        $loadedRecord = $this->replayLoader->loadRecord($recordPath);
        $this->replayCombinator->setLoader($loadedRecord);
        return $this;
    }

    private function getCombinedJS(string $webHookUrl, string $instanceID, int $timeOut = 7000, array $puppeteerLaunchOptions = []): string
    {
        return $this->replayCombinator->combineWith($webHookUrl, $instanceID, $timeOut, $puppeteerLaunchOptions);
    }

    public function package(string $webHookUrl, string $instanceID, int $timeOut = 7000, array $puppeteerLaunchOptions = []): string
    {
        $combinedJS = $this->getCombinedJS($webHookUrl, $instanceID, $timeOut, $puppeteerLaunchOptions);
        $appPackage = $this->nodeAppPackagerService->packageApp(self::NODE_APP_NAME, $combinedJS);
        return $appPackage;
    }

}