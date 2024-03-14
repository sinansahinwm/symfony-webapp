<?php namespace App\Controller\Webhook;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface WebHookInterface
{
    public function hook(Request $request): Response;

    public function getAbsoluteUrl(): string;

}