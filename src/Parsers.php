<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile($absolutePathToFile)
{
    $expansion = pathinfo($absolutePathToFile, PATHINFO_EXTENSION);
    if ($expansion === "json") {
        $fileContents = file_get_contents($absolutePathToFile);
        return json_decode($fileContents, $associative = true);
    }
    if ($expansion === "yml" || $expansion === "yaml") {
        return $fileContents = Yaml::parseFile($absolutePathToFile);
    }
}
