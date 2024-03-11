<?php namespace App\Service\ChromeRecordPlayer;

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Exception\JsonException;

class ChromeRecordReader
{

    private ?string $recordTitle = NULL;

    private array $recordSteps = [];

    private AbstractBrowser $myBrowser;

    public function loadRecord(string $recordPath): self
    {
        if (file_exists($recordPath)) {
            $recordContent = file_get_contents($recordPath);
            $jsonIsValid = json_validate($recordContent);

            if ($jsonIsValid === FALSE) {
                throw new JsonException();
            }

            if ($recordContent !== FALSE) {
                $parsedRecord = json_decode($recordContent);
                $this->recordTitle = $parsedRecord->title;
                $this->recordSteps = $this->readSteps($parsedRecord->steps);
                $this->myBrowser = new ChromeBrowser();
                return $this;
            }
        }

        throw new FileNotFoundException();
    }

    public function isSuccess(): bool
    {
        return ($this->recordTitle !== NULL) and (count($this->recordSteps) > 1);
    }

    public function getRecordTitle(): string
    {
        return $this->recordTitle;
    }

    public function getRecordSteps(): array
    {
        return $this->recordSteps;
    }

    private function readSteps(array $recordSteps): array
    {
        return $recordSteps;
    }

    public function getBrowser(): AbstractBrowser
    {
        return $this->myBrowser;
    }

}