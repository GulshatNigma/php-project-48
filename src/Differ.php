<?php

namespace Differ\Differ;

use function Functional\flatten;
use function Differ\Parsers\parseFile;
use function Differ\Formatter\Stylish\getFormatStylish;

function genDiff($pathToFile1, $pathToFile2, $format = "stylish")
{
    $file1Content = parseFile(realpath($pathToFile1));
    $file2Content = parseFile(realpath($pathToFile2));

    $result = getUniqueKeysOfFiles($file1Content, $file2Content);
    return getFormatStylish($result);
}

function getUniqueKeysOfFiles($file1Content, $file2Content)
{
    $file1Keys = array_keys($file1Content);
    $file2Keys = array_keys($file2Content);
    $filesKeys = array_unique(array_merge($file1Keys, $file2Keys));
    sort($filesKeys, SORT_STRING);
    return array_map(fn($key) => makeDifferenceCheck($file1Content, $file2Content, $key), $filesKeys);
}

function makeDifferenceCheck($file1Content, $file2Content, $key)
{
    $file1Value = $file1Content[$key] ?? null;
    $file2Value = $file2Content[$key] ?? null;
    if (is_array($file1Value) && is_array($file2Value)) {
        $value = getUniqueKeysOfFiles($file1Value, $file2Value);
        return ["category" => "parent node", "key" => $key, "value" => $value];
    }
    if (!array_key_exists($key, $file2Content)) {
        $value = getLines($file1Value);
        return ["category" => "deleted", "key" => $key, "value" => $value];
    }
    if (!array_key_exists($key, $file1Content)) {
        $value = getLines($file2Value);
        return ["category" => "added", "key" => $key, "value" => $value];
    }
    if ($file1Value !== $file2Value) {
        $value1 = getLines($file1Value) ?? null;
        $value2 = getLines($file2Value) ?? null;
        return ["category" => "changed",  "key" => $key, "value" => $value1, "value2" => $value2,];
    }
    return ["category" => "unchanged", "key" => $key, "value" => $file1Value];
}

function getLines($fileContent)
{
    $iter = function ($fileContent) use (&$iter){
        if (!is_array($fileContent)) {
            return toString($fileContent);
        }

        $filesKeys = array_keys($fileContent);
        return array_map(
            function ($key) use ($fileContent, $iter) {
                $value = $fileContent[$key];
                if (is_array($value)) {
                    $value = $iter($value);
                }
                return ["category" => "unchanged", "key" => $key, "value" => $value];
            },
            $filesKeys
        );
    };
    return $iter($fileContent);
}

function toString($value)
{
    return trim(var_export($value, true), "'");
}
