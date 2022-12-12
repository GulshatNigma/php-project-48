<?php

namespace Differ\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parseFile($absolutePathToFile)
{
    $expansion = pathinfo($absolutePathToFile, PATHINFO_EXTENSION);
    switch ($expansion) {
        case "json":
            $fileContent = file_get_contents($absolutePathToFile);
            return json_decode($fileContent, $associative = true);
        case "yaml":
            return Yaml::parseFile($absolutePathToFile);
        case "yml":
            return Yaml::parseFile($absolutePathToFile);
        default:
            throw new Exception("Unknown expansion");
    }
}
