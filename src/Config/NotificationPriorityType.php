<?php namespace App\Config;

class NotificationPriorityType extends EnumType
{
    public const LOW = "LOW";
    public const NORMAL = "NORMAL";
    public const HIGH = "HIGH";

    protected static string $name = 'notification_priority';

}