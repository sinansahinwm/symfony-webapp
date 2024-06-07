<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class JsonPrettyPrintExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function jsonPrettyPrint($rawJSON): string
    {
        $jsonString = is_string($rawJSON) ? $rawJSON : json_encode($rawJSON);
        $encodedJSON = json_decode($jsonString, TRUE, 512,JSON_PRETTY_PRINT);
        $prettiedJSON = json_encode($encodedJSON, JSON_PRETTY_PRINT);
        return $prettiedJSON;
    }
}
