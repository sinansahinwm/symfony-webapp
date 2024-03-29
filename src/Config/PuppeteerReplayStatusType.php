<?php namespace App\Config;

class PuppeteerReplayStatusType extends EnumType
{
    public const UPLOAD = "UPLOAD";
    public const PROCESSING = "PROCESSING";
    public const COMPLETED = "COMPLETED";
    public const ERROR = "ERROR";

    protected static string $name = 'puppeteer_replay_status';

}