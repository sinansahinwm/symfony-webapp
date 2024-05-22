<?php

namespace App\Message;

final class ProccessWebScrapingRequestMessage
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
