<?php

namespace Differ\Differ;

use Exception;

use function Functional\sort;
use function Differ\Parser\parseFile;
use function Differ\DesiredFormat\getDesiredFormat;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "stylish")
{
    $file1Content = getFileContent($pathToFile1);
    $file2Content = getFileContent($pathToFile2);
    $differenceTree = builDifferenceTree($file1Content, $file2Content);
    return getDesiredFormat($format, $differenceTree);
}

function getFileContent(string $pathToFile)
{
    $absolutePathToFile = realpath($pathToFile);
    if ($absolutePathToFile === false) {
        return new Exception("File does not exist");
    }
    $fileContent = file_get_contents($absolutePathToFile);
    $expansion = pathinfo($absolutePathToFile, PATHINFO_EXTENSION);
    return parseFile($expansion, $fileContent);
}

function builDifferenceTree(array $file1Content, array $file2Content)
{
    $file1Keys = array_keys($file1Content);
    $file2Keys = array_keys($file2Content);
    $filesKeys = array_unique(array_merge($file1Keys, $file2Keys));
    $sortFilesKeys = sort($filesKeys, function ($leftKey, $rightKey) {
        return strcmp($leftKey, $rightKey);
    });
    return array_map(fn($key) => findDifference($file1Content, $file2Content, $key), $sortFilesKeys);
}

function findDifference(array $file1Content, array $file2Content, string $key)
{
    $getChildren = function ($fileContent) use (&$getChildren) {
        if (!is_array($fileContent)) {
            return $fileContent === null ? "null" : trim(var_export($fileContent, true), "'");
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

    $file1Value = $file1Content[$key] ?? null;
    $file2Value = $file2Content[$key] ?? null;

    if (is_array($file1Value) && is_array($file2Value)) {
        $value = builDifferenceTree($file1Value, $file2Value);
        $difference = ["category" => "parent node", "key" => $key, "value" => $value];
    } elseif (!array_key_exists($key, $file2Content)) {
        $value = $getChildren($file1Value);
        $difference = ["category" => "deleted", "key" => $key, "value" => $value];
    } elseif (!array_key_exists($key, $file1Content)) {
        $value = $getChildren($file2Value);
        $difference = ["category" => "added", "key" => $key, "value" => $value];
    } elseif ($file1Value !== $file2Value) {
        $value = $getChildren($file1Value);
        $value2 = $getChildren($file2Value);
        $difference = ["category" => "changed",  "key" => $key, "value" => $value, "value2" => $value2,];
    } else {
        $difference = ["category" => "unchanged", "key" => $key, "value" => $file1Value];
    }
    return $difference;
}
