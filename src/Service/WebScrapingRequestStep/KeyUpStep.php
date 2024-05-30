<?php namespace App\Service\WebScrapingRequestStep;

class KeyUpStep implements WebScrapingRequestStepInterface
{


    public function __construct(private readonly string $key)
    {
    }

    public function getStepData(): array
    {
        return [
            "type" => "keyUp",
            "target" => "main",
            "key" => $this->key
        ];
    }

}