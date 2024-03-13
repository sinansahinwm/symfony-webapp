<?php namespace App\Service\NodeApp;

interface NodeAppInterface
{
    public static function getName(): string;

    public function getEntrypointContent(): string;

    public function releaseApp(): void;

}