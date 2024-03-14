<?php namespace App\Controller\Webhook;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface WebHookInterface
{

    public function getAbsoluteUrl(): string;

}