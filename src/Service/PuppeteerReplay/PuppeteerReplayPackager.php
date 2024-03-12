<?php namespace App\Service\PuppeteerReplay;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

class PuppeteerReplayPackager
{

    private PuppeteerReplayCombinator $replayCombinator;

    public function __construct(PuppeteerReplayCombinator $replayCombinator, private PuppeteerReplayLoader $replayLoader, private ContainerBagInterface $containerBag)
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
        return $this->preparePackage($combinedJS);
    }

    private function preparePackage(string $combinedJS)
    {
        $myFS = new Filesystem();
        $zipFilename = $myFS->tempnam('/tmp', 'puppeteer_replayer_package_', '.zip');
        $replayerFilesBasePath = $this->containerBag->get("app.projectDir") . "/assets/puppeteer_replayer";
        $packageContents = [
            "extension.js" => $replayerFilesBasePath . "/extension.js",
            "package.json" => $replayerFilesBasePath . "/package.json",
            "package-lock.json" => $replayerFilesBasePath . "/package-lock.json",
        ];
        $packageZip = new ZipArchive();
        if ($packageZip->open($zipFilename, ZipArchive::CREATE) === TRUE) {
            foreach ($packageContents as $entryName => $filePath) {
                $packageZip->addFromString($entryName, file_get_contents($filePath));
            }
            $packageZip->addFromString('index.js', $combinedJS);
            $packageZip->close();
        }
        return $zipFilename;
    }

}