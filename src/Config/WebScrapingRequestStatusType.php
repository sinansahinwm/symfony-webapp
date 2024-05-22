<?php namespace App\Config;

class WebScrapingRequestStatusType extends EnumType
{
    public const NEWLY_CREATED = "NEWLY_CREATED";

    protected static string $name = 'web_scraping_request_status';

}