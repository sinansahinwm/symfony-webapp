<?php namespace App\Config;

class MessageBusDelays
{
    public const SEND_VERIFY_EMAIL_AFTER_REGISTRATION = 1000;
    public const SEND_WELCOME_EMAIL_AFTER_EMAIL_VERIFICATION = 5000;

    public const SEND_RESET_PASSWORD_EMAIL_AFTER_REQUESTED = 5000;
    public const SEND_INVITE_EMAIL_AFTER_PERSISTED = 5000;

}