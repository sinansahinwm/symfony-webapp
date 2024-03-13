<?php

namespace App\Message;

final class NodeAppPackageDeliveryMessage
{

    public function __construct(private string $packagePath)
    {
    }

    public function getPackagePath(): string
    {
        return $this->packagePath;
    }

    public function setPackagePath(string $packagePath): void
    {
        $this->packagePath = $packagePath;
    }

}
