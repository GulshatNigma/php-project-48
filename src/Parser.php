<?php

namespace Differ\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parseFile(string $expansion, string $fileContent): array
{
    switch ($expansion) {
        case "json":
            return json_decode($fileContent, $associative = true);
        case "yaml":
        case "yml":
            return Yaml::parse($fileContent);
        default:
            throw new Exception("Unknown expansion");
    }
}
