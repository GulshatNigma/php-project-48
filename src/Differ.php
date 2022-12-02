<?php

namespace Differ\Differ;

use function Functional\flatten;
use function Differ\Parsers\parseFile;

function genDiff($pathToFile1, $pathToFile2)
{
    $file1Content = getContentsOfFile($pathToFile1);
    $file2Content = getContentsOfFile($pathToFile2);

    $result = getResult($file1Content, $file2Content);

    return "{\n$result\n}\n";
}

function getResult($file1Content, $file2Content)
{
    $iter = function ($file1Content, $file2Content) {
        $filesKeys = getUniqueKeysOfFiles($file1Content, $file2Content);
        $result = array_map(
            function ($key) use ($file1Content, $file2Content) {
                return makeDifferenceCheck($file1Content, $file2Content, $key);
            },
            $filesKeys
        );
        return implode("\n", $result);
    };
    $result = $iter($file1Content, $file2Content);
    return $result;
}

function makeDifferenceCheck($file1Content, $file2Content, $key)
{
    if (!array_key_exists($key, $file2Content)) {
        $file1Value = toString($file1Content[$key]);
        return "- {$key}: {$file1Value}";
    }
    if (!array_key_exists($key, $file1Content)) {
        $file2Value = toString($file2Content[$key]);
        return "+ {$key}: {$file2Value}";
    }
    $file1Value = $file1Content[$key];
    $file2Value = $file2Content[$key];
    if (!is_array($file1Value) || !is_array($file2Value)) {
        $file1Value = toString($file1Content[$key]);
        $file2Value = toString($file2Content[$key]);
        if ($file1Value !== $file2Value) {
            return "- {$key}: {$file1Value}" . "\n" . "+ {$key}: {$file2Value}";
        }
            return "  {$key}: {$file1Value}";
    }
    return "  {$key}:\n" . getResult($file1Value, $file2Value);
}

function getContentsOfFile($pathToFile)
{
    $absolutePathToFile = realpath($pathToFile);
    return parseFile($absolutePathToFile);
}

function getUniqueKeysOfFiles($file1Content, $file2Content)
{
    $file1Keys = array_keys($file1Content);
    $file2Keys = array_keys($file2Content);
    $filesKeys = array_unique(array_merge($file1Keys, $file2Keys));
    sort($filesKeys, SORT_STRING);
    return $filesKeys;
}

function toString($value)
{
    return trim(var_export($value, true), "'");
}
