<?php

namespace Differ\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parse(string $extension, string $content): array
{
    switch ($extension) {
        case "json":
            return json_decode($content, true);
        case "yaml":
        case "yml":
            return Yaml::parse($content);
        default:
            throw new Exception("Unknown extension");
    }
}
