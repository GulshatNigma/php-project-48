<?php

namespace Differ\Formatter\Stylish;

function getFormatStylish($tree, $depth = 1)
{
    $ident = str_repeat("    ", $depth);
    $result = array_map(function ($node) use ($ident, $depth) {
        $type = getCategory($node);
        if ($type === false) {
            $key = toString($node[0]);
            $value = toString($node[1]);
            return "$ident  $key: $value";
        }
        $key = getKey($node);
        $value = getValue($node);
        if (is_array($value)) {
            $value = getFormatStylish($value, $depth + 1);
        }
        if (is_array($value) && gettype($key) !== "integer") {
            $value = getFormatStylish($value, $depth + 1);
        }

        if ($type === "changed") {
            $value2 = getValue2($node);
            return "$ident- $key: $value" . "\n" . "$ident+ $key: $value2";
        }
        if ($type  === "parent node") {
            return "$ident  $key: $value";
        }
    
        if ($type === "deleted") {
            return "$ident- $key: $value";
        }
    
        if ($type === "added") {
            return "$ident+ $key: $value";
        }
    
        if ($type === "unchanged") {
            return "$ident  $key: " . $value;
        }
    
    }, $tree);
    $result = implode("\n", $result);
    return "{\n$result\n}\n";
    }

function getIndent($depth = 1, $replacer = "    ")
{
    $ident = str_repeat($replacer, $depth);
    return $ident;
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
