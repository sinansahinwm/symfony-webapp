<?php namespace App\Message;

class HandleWebScrapingRequestAfterCompletedMessage
{
    public function __construct(private int $webScrapingRequestID)
    {
    }

    public function getWebScrapingRequestID(): int
    {
        return $this->webScrapingRequestID;
    }

    public function setWebScrapingRequestID(int $webScrapingRequestID): void
    {
        $this->webScrapingRequestID = $webScrapingRequestID;
    }


}