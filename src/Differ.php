<?php

namespace Differ\Differ;

use function Functional\flatten;

function genDiff($pathToFile1, $pathToFile2)
{
    $file1Content = getContentsOfFile($pathToFile1);
    $file2Content = getContentsOfFile($pathToFile2);

    $generalDataOfFiles = array_intersect_assoc($file1Content, $file2Content);
    $filesKeys = getUniqueKeysOfFiles($file1Content, $file2Content);

    $result = array_reduce($filesKeys, function ($acc, $key) use ($file1Content, $file2Content, $generalDataOfFiles) {                      
        $acc[] = makeDifferenceCheck($file1Content, $file2Content, $generalDataOfFiles, $key);
        return $acc;
    }, []);
    
    $result = implode("\n  ", $result);
    return "{\n  $result\n}\n";
}

function getContentsOfFile($pathToFile)
{
    $absolutePathToFile = realpath($pathToFile);
    $fileContents = file_get_contents($absolutePathToFile);
    return json_decode($fileContents, $associative = true);
}

function getUniqueKeysOfFiles($file1Content, $file2Content)
{
    $file1Keys = array_keys($file1Content);
    $file2Keys = array_keys($file2Content);
    $filesKeys = array_unique(array_merge($file1Keys, $file2Keys));
    sort($filesKeys, SORT_STRING);
    return $filesKeys;
}

function makeDifferenceCheck($file1Content, $file2Content, $generalDataOfFiles, $key)
{
    if (array_key_exists($key, $file1Content) && !array_key_exists($key, $file2Content)) {
       $file1Value = getValue($file1Content, $key);
       return "- {$key}: {$file1Value}";
    } 
    if (!array_key_exists($key, $file1Content) && array_key_exists($key, $file2Content)) {
        $file2Value = getValue($file2Content, $key);
       return "+ {$key}: {$file2Value}";
    } 
    if (array_key_exists($key, $generalDataOfFiles)) {
        return "  {$key}: {$generalDataOfFiles[$key]}";
    } else {
        $file1Value = getValue($file1Content, $key);
        $file2Value = getValue($file2Content, $key);
        return "- {$key}: {$file1Value}" . "\n  " . "+ {$key}: {$file2Value}";
    }       
}

function getValue($fileContent, $key)
{
    if (gettype($fileContent[$key]) === "boolean") {
        return $fileContent[$key] === true ? "true" : "false";
    }
    return $fileContent[$key];
}
