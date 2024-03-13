<?php namespace App\Service\NodeApp\PuppeteerReplayer;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class PuppeteerReplayLoader
{

    private string $content;
    const RECORD_MUST_CONTAINS = [
        'run()',
    ];

    public function loadRecord(string $path): self
    {
        $jsContent = file_get_contents($path);
        if ($jsContent !== FALSE) {
            if ($this->checkLoadedContent($jsContent) === TRUE) {
                $this->content = $jsContent;
                return $this;
            } else {
                throw new FileNotFoundException();
            }
        } else {
            throw new FileNotFoundException();
        }
    }

    private function checkLoadedContent(string $loadedContentStr): bool
    {
        foreach (self::RECORD_MUST_CONTAINS as $mustContain) {
            if (str_contains($loadedContentStr, $mustContain) === FALSE) {
                return FALSE;
            }
        }
        return TRUE;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

}