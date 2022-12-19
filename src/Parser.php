<?php

namespace Differ\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parseFile(string $absolutePathToFile)
{
    $expansion = pathinfo($absolutePathToFile, PATHINFO_EXTENSION);
    $fileContent = file_get_contents($absolutePathToFile);
    if ($fileContent === false) {
        return new Exception("File read error");
    }
    switch ($expansion) {
        case "json":
            return json_decode($fileContent, $associative = true);
        case "yaml":
            return Yaml::parseFile($absolutePathToFile);
        case "yml":
            return Yaml::parseFile($absolutePathToFile);
        default:
            throw new Exception("Unknown expansion");
    }
}
