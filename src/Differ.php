<?php

namespace Differ\Differ;

use Exception;

use function Functional\sort;
use function Differ\Parser\parseFile;
use function Differ\Formatter\getDesiredFormat;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "stylish"): string
{
    $file1Content = makeParseFile($pathToFile1);
    $file2Content = makeParseFile($pathToFile2);

    $differenceTree = buildDifferenceTree($file1Content, $file2Content);
    return getDesiredFormat($format, $differenceTree);
}




function getExpansion(string $pathToFile): string
{
    return $expansion = pathinfo(getAbsolutePathToFile($pathToFile), PATHINFO_EXTENSION);
}

function getAbsolutePathToFile(string $pathToFile): string
{
    return realpath($pathToFile) === false ? new Exception("File does not exist") : realpath($pathToFile);
}

function getFileContent(string $pathToFile): string
{
    return file_get_contents(getAbsolutePathToFile($pathToFile)) === false
            ? new Exception("File read error")
            : file_get_contents(getAbsolutePathToFile($pathToFile));
}

function makeParseFile(string $pathToFile): array
{
    return parseFile(getExpansion($pathToFile), getFileContent($pathToFile));
}





function buildDifferenceTree(array $file1Content, array $file2Content): array
{
    $file1Keys = array_keys($file1Content);
    $file2Keys = array_keys($file2Content);
    $filesKeys = array_unique(array_merge($file1Keys, $file2Keys));

    $sortFilesKeys = sort($filesKeys, function ($leftKey, $rightKey) {
        return strcmp($leftKey, $rightKey);
    });

    return array_map(fn($key) => findDifference($file1Content, $file2Content, $key), $sortFilesKeys);
}




function findDifference(array $file1Content, array $file2Content, string $key): array
{
    $file1Value = $file1Content[$key] ?? null;
    $file2Value = $file2Content[$key] ?? null;

    if (is_array($file1Value) && is_array($file2Value)) {
        $value = buildDifferenceTree($file1Value, $file2Value);
        $difference = ["category" => "has children", "key" => $key, "value" => $value];
    } elseif (!array_key_exists($key, $file2Content)) {
        $value = getChildren($file1Value);

        $difference = ["category" => "deleted", "key" => $key, "value" => $value];
    } elseif (!array_key_exists($key, $file1Content)) {
        $value = getChildren($file2Value);

        $difference = ["category" => "added", "key" => $key, "value" => $value];
    } elseif ($file1Value !== $file2Value) {
        $value = getChildren($file1Value);
        $value2 = getChildren($file2Value);

        $difference = ["category" => "changed",  "key" => $key, "value" => $value, "value2" => $value2,];
    } else {
        $difference = ["category" => "unchanged", "key" => $key, "value" => $file1Value];
    }
    return $difference;
}

function getChildren(mixed $fileContent)
{
    $getChildren = function ($fileContent) use (&$getChildren) {
        if (!is_array($fileContent)) {
            return getStringContent($fileContent);
        }

        $fileKeys = array_keys($fileContent);
        return array_map(
            function ($key) use ($fileContent, $getChildren) {
                $value = is_array($fileContent[$key]) ? $getChildren($fileContent[$key]) : $fileContent[$key];
                return ["category" => "unchanged", "key" => $key, "value" => $value];
            },
            $fileKeys
        );
    };

    return $getChildren($fileContent);
}

function getStringContent(mixed $fileContent)
{
    return $fileContent === null ? "null" : trim(var_export($fileContent, true), "'");
}
