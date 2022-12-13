<?php

namespace Differ\Formatter\Plain;

function getFormat($tree)
{
    $iter = function ($tree, $parentKey = "") use (&$iter) {
        $lines = array_map(function ($node) use ($iter, $parentKey) {
            $type = getCategory($node);
            $key = getKey($node);
            $value = normalizeValue(getValue($node), $type);
            switch ($type) {
                case "parent node":
                    if (is_array($value)) {
                        $parentKey .= "$key.";
                        $value = $iter($value, $parentKey);
                    }
                    return "$value";
                case "changed":
                    $value2 = getValue2($node);
                    $value2 = normalizeValue($value2, $type);
                    return "Property '$parentKey$key' was updated. From $value to $value2";
                case "deleted":
                    return "Property '$parentKey$key' was removed";
                case "added":
                    return "Property '$parentKey$key' was added with value: $value";
                default:
                    break;
            }
        }, $tree);
        $line = [...$lines];
        $line = array_filter($line, fn($path) => $path !== null);
        return implode("\n", $line);
    };
    $result = $iter($tree);
    return $result . "\n";
}

function normalizeValue($value, $type)
{
    if (gettype($value) === 'string') {
        $value = "'$value'";
    }
    if (is_array($value) && $type !== "parent node") {
        $value = "[complex value]";
    }
    if ($value === "'false'" || $value === "'true'" || $value === "'null'") {
        $value = toString($value);
    }
    return $value;
}

function toString($value)
{
    return trim($value, "'");
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
