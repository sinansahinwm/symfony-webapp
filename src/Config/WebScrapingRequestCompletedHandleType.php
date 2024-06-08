<?php namespace App\Config;

class WebScrapingRequestCompletedHandleType extends EnumType
{

    public const HANDLE_NULL = 'HANDLE_NULL';
    public const HANDLE_EXTRACT_PRODUCTS = 'HANDLE_EXTRACT_PRODUCTS';

    protected static string $name = 'web_scraping_request_completed_handle';

}