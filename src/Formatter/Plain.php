<?php

namespace Differ\Formatter\Plain;

function getFormat(array $tree)
{
    $iter = function ($tree, $parentKey = "") use (&$iter) {
        $lines = array_map(function ($node) use ($iter, $parentKey) {
            $type = getCategory($node);
            $key = getKey($node);
            $value = normalizeValue(getValue($node), $type);
            if ($type === "parent node") {
                if (is_array($value)) {
                    $parentKey .= "$key.";
                    $value = $iter($value, $parentKey);
                }
                $resultLine = "$value";
            } else {
                $resultLine = getResultByType($type, $key, $value, $node, $parentKey);
            }
            return $resultLine;
        }, $tree);
        $line = [...$lines];
        $line = array_filter($line, fn($path) => $path !== null);
        return implode("\n", $line);
    };
    $result = $iter($tree);
    return $result;
}

function getResultByType(string $type, string $key, $value, array $node, string $parentKey)
{
    switch ($type) {
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
}

function normalizeValue($value, string $type)
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
    if (in_array($value, ["'0'", "'1'", "'3'", "'4'", "'5'", "'6'", "'7'", "'8'", "'9'"])) {
        $value = toString($value);
    }
    return $value;
}

function toString(string $value)
{
    return trim($value, "'");
}

function getCategory(array $node)
{
    return $node["category"];
}

function getKey(array $node)
{
    return $node["key"];
}

function getValue(array $node)
{
    return $node["value"];
}

function getValue2(array $node)
{
    return $node["value2"];
}
