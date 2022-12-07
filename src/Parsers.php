<?php

namespace Differ\Parsers;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parseFile($absolutePathToFile)
{
    $expansion = pathinfo($absolutePathToFile, PATHINFO_EXTENSION);
    switch ($expansion) {
        case "json":
            $fileContents = file_get_contents($absolutePathToFile);
            return json_decode($fileContents, $associative = true);
        case "yaml":
            return $fileContents = Yaml::parseFile($absolutePathToFile);
        case "yml":
            return $fileContents = Yaml::parseFile($absolutePathToFile);
        default:
            throw new Exception("Unknown expansion");
    }
}
