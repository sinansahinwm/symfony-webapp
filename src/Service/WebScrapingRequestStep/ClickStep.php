<?php namespace App\Service\WebScrapingRequestStep;

class ClickStep implements WebScrapingRequestStepInterface
{


    public function __construct(private readonly array $selectors)
    {
    }

    public function getStepData(): array
    {
        return [
            "type" => "click",
            "target" => "main",
            "selectors" => [
                $this->selectors
            ]
        ];
    }

}