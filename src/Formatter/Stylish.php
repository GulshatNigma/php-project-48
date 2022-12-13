<?php

namespace Differ\Formatter\Stylish;

function getFormat($tree, $depth = 1)
{
    $iter = function ($tree, $depth) use (&$iter) {
        $indentStart = str_repeat("  ", $depth);
        $indentEnd = str_repeat("  ", $depth - 1);
        $lines = array_map(function ($node) use ($indentStart, $depth, $indentEnd, $iter) {
            $type = getCategory($node);
            $key = getKey($node);
            $value = getValue($node);
            if (is_array($value)) {
                $value = $iter($value, $depth + 2);
            }
            return getResultByType($type, $indentStart, $key, $value, $node);
        }, $tree);
        $result = ["{", ...$lines, "{$indentEnd}}"];
        return implode("\n", $result);
    };
    $line = $iter($tree, 1);
    return $line;
}

function getResultByType($type, $indentStart, $key, $value, $node)
{
    switch ($type) {
        case "parent node":
            $result =  "$indentStart  $key: $value";
            break;
        case "changed":
            $value2 = getValue2($node);
            $line1 = "$indentStart- $key:" . "\n" . "$indentStart+ $key: $value2";
            $line2 = "$indentStart- $key: $value" . "\n" . "$indentStart+ $key: $value2";
            $result = $value === "" ? $line1 : $line2;
            break;
        case "deleted":
            $result = "$indentStart- $key: $value";
            break;
        case "added":
            $result = "$indentStart+ $key: $value";
            break;
        case "unchanged":
            $result = "$indentStart  $key: $value";
            break;
        default:
            break;
    }
        return $result;
}

function getCategory($node)
{
    return $node["category"];
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
