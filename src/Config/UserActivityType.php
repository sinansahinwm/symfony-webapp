<?php namespace App\Config;

class UserActivityType extends EnumType
{
    public const LOGIN = "LOGIN";
    public const CREATE_TEAM = "CREATE_TEAM";
    public const RECEIVE_TEAM_INVITE = "RECEIVE_TEAM_INVITE";

    protected static string $name = 'user_activity';

}