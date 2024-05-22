<?php namespace App\Config;

class WebScrapingRequestStatusType extends EnumType
{
    public const NEWLY_CREATED = "NEWLY_CREATED";
    public const FORWARDED_TO_REMOTE_SERVER = "FORWARDED_TO_REMOTE_SERVER";
    public const PING_PONG_FAILED = "PING_PONG_FAILED";
    public const CONSUMING = "CONSUMING";
    public const CONSUME_ERROR = "CONSUME_ERROR";
    public const REMOTE_STATUS_FAILED_WHEN_CONSUMING = "REMOTE_STATUS_FAILED_WHEN_CONSUMING";
    public const COMPLETED = "COMPLETED";

    protected static string $name = 'web_scraping_request_status';

}