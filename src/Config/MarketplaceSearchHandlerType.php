<?php namespace App\Config;

class MarketplaceSearchHandlerType extends EnumType
{
    public const STEPS = "STEPS";
    public const NAVIGATION = "NAVIGATION";

    protected static string $name = 'marketplace_search_handler';

}