<?php namespace App\Config;

class UserActivityType extends EnumType
{
    public const LOGIN = "LOGIN";
    public const CREATE_TEAM = "CREATE_TEAM";
    public const RECEIVE_TEAM_INVITE = "RECEIVE_TEAM_INVITE";
    public const USER_PASSWORD_CHANGED = "USER_PASSWORD_CHANGED";

    protected static string $name = 'user_activity';

}