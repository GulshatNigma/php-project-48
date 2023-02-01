<?php

namespace Differ\Differ;

use Exception;

use function Functional\sort;
use function Differ\Parser\parse;
use function Differ\Formatter\getDesiredFormat;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "stylish"): string
{
    $content1 = makeParseFile($pathToFile1);
    $content2 = makeParseFile($pathToFile2);

    $differenceTree = buildDifferenceTree($content1, $content2);
    return getDesiredFormat($format, $differenceTree);
}

function makeParseFile(string $pathToFile): array
{
    return parse(getExtension($pathToFile), getFileContent($pathToFile));
}

function getExtension(string $pathToFile): string
{
    return pathinfo(getAbsolutePathToFile($pathToFile), PATHINFO_EXTENSION);
}

function getAbsolutePathToFile(string $pathToFile): string
{
    return realpath($pathToFile) === false ? new Exception("File does not exist") : realpath($pathToFile);
}

function getFileContent(string $pathToFile): string
{
    $content = file_get_contents(getAbsolutePathToFile($pathToFile));

    if ($content === false) {
        throw new Exception("File read error");
    }
    return $content;
}

function buildDifferenceTree(array $content1, array $content2): array
{
    $keys = array_unique(array_merge(array_keys($content1), array_keys($content2)));
    $sortedKeys = sort($keys, function ($leftKey, $rightKey) {
        return strcmp($leftKey, $rightKey);
    });

    return array_map(fn($key) => findDifference($content1, $content2, $key), $sortedKeys);
}

function findDifference(array $content1, array $content2, string $key): array
{
    $contentValue1 = $content1[$key] ?? null;
    $contentValue2 = $content2[$key] ?? null;

    if (is_array($contentValue1) && is_array($contentValue2)) {
        $value = buildDifferenceTree($contentValue1, $contentValue2);
        return ["category" => "has children", "key" => $key, "value" => $value];
    }

    $value = getChildren($contentValue1);
    $value2 = getChildren($contentValue2);

    if (!array_key_exists($key, $content2)) {
        return ["category" => "deleted", "key" => $key, "value" => $value];
    }

    if (!array_key_exists($key, $content1)) {
        return ["category" => "added", "key" => $key, "value" => $value2];
    }

    if ($contentValue1 !== $value2) {
        return ["category" => "changed",  "key" => $key, "value" => $value, "value2" => $value2];
    }

    return ["category" => "unchanged", "key" => $key, "value" => $contentValue1];
}

function getChildren(mixed $content): mixed
{
    $getChildren = function ($content) use (&$getChildren) {
        if (!is_array($content)) {
            return $content;
        }

        $keys = array_keys($content);
        return array_map(
            function ($key) use ($content, $getChildren) {
                $value = is_array($content[$key]) ? $getChildren($content[$key]) : $content[$key];
                return ["category" => "unchanged", "key" => $key, "value" => $value];
            },
            $keys
        );
    };

    return $getChildren($content);
}
