<?php

namespace Differ\Formatter\Stylish;

function getFormatStylish($tree, $depth = 1)
{
    $identStart = str_repeat("  ", $depth);
    $identEnd = str_repeat("  ", $depth - 1);
    $result = array_map(function ($node) use ($identStart, $depth, $identEnd) {
        $type = getCategory($node);
        $key = getKey($node);
        $value = getValue($node);
        if (is_array($value)) {
            $value = getFormatStylish($value, $depth + 2);
            $value = "{\n" . $identStart . $value . "\n" . $identStart . "  }";
        }

        if ($type === "changed") {
            $value2 = getValue2($node);
            return "$identStart- $key: $value" . "\n" . "$identStart+ $key: $value2";
        }
        if ($type  === "parent node") {
            return "$identStart  $key: $value";
        }
    
        if ($type === "deleted") {
            return "$identStart- $key: $value";
        }
    
        if ($type === "added") {
            return "$identStart+ $key: $value";
        }
    
        if ($type === "unchanged") {
            return "$identStart  $key: " . $value;
        }
    
    }, $tree);
    $result = implode("\n", $result);
    return "$result";
}

function getCategory($node)
{
    return array_key_exists("category", $node) ? $node["category"] : false;
}

function getKey($node)
{
    return $node["key"];
}

function getValue($node)
{
    return $node["value"];
}

function getValue2($node)
{
    return $node["value2"];
}

function toString($value)
{
    return trim(var_export($value, true), "'");
}
