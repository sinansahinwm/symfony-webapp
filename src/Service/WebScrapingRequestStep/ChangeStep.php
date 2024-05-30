<?php namespace App\Service\WebScrapingRequestStep;

class ChangeStep implements WebScrapingRequestStepInterface
{


    public function __construct(private readonly string $value, private readonly array $selectors)
    {
    }

    public function getStepData(): array
    {
        return [
            "type" => "change",
            "value" => $this->value,
            "selectors" => [
                $this->selectors
            ]
        ];
    }

}