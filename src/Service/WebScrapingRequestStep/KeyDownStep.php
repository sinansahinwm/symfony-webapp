<?php namespace App\Service\WebScrapingRequestStep;

class KeyDownStep implements WebScrapingRequestStepInterface
{


    public function __construct(private readonly string $key)
    {
    }

    public function getStepData(): array
    {
        return [
            "type" => "keyDown",
            "target" => "main",
            "key" => $this->key
        ];
    }

}