<?php namespace App\Service\NodeApp\PuppeteerReplayer;

class PuppeteerReplayCombinator
{
    private PuppeteerReplayLoader $loader;

    const IMPORTS_DELIMETER = 'export async function';
    const RUN_DELIMETER = 'run()';

    public function setLoader(PuppeteerReplayLoader $loader): self
    {
        $this->loader = $loader;
        return $this;
    }

    public function combineWith(string $webHookUrl, string $instanceID, int $timeOut = 7000, array $puppeteerLaunchOptions = []): string
    {
        $loadedContent = $this->loader->getContent();
        $oldValues = [
            self::IMPORTS_DELIMETER,
            self::RUN_DELIMETER,
        ];
        $newValues = [
            implode(PHP_EOL, $this->getImportLines()) . PHP_EOL . PHP_EOL . self::IMPORTS_DELIMETER,
            implode(PHP_EOL, $this->getRunLines($webHookUrl, $instanceID, $timeOut, $puppeteerLaunchOptions)),
        ];
        return str_replace($oldValues, $newValues, $loadedContent);
    }

    private function getImportLines(): array
    {
        return [
            'import PuppeteerBridgeExtension from "./extension.js"',
            'import puppeteer from "puppeteer";',
        ];
    }

    private function getRunLines(string $webHookUrl, string $instanceID, int $timeOut = 7000, array $puppeteerLaunchOptions = []): array
    {
        $puppeteerOptionsJson = (count($puppeteerLaunchOptions) > 0) ? json_encode($puppeteerLaunchOptions) : '{}';

        return [
            "const myBrowser = await puppeteer.launch($puppeteerOptionsJson);",
            'const myPage = await myBrowser.newPage();',
            'const myExtension = new PuppeteerBridgeExtension(myBrowser, myPage, ' . $timeOut . ', "' . $webHookUrl . '", "' . $instanceID . '");',
            'await run(myExtension);'
        ];
    }

}