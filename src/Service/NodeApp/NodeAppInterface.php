<?php namespace App\Service\NodeApp;

interface NodeAppInterface
{
    public function getName(): string;

    public function getEntrypointContent(): string;

}