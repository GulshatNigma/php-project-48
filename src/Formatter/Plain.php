<?php

namespace Differ\Formatter\Plain;

function getFormat(array $tree)
{
    $iter = function ($tree, $parentKey = []) use (&$iter) {
        $lines = array_map(function ($node) use ($iter, $parentKey) {
            $type = getCategory($node);
            $key = getKey($node);
            $parentKey[] = $key;
            $value = is_array($node["value"])
            ? normalizeArrayValue(getValue($node), $type, $parentKey, $iter)
            : normalizeValue(getValue($node), $type);
            $resultLine = getResultByType($type, $key, $value, $node, $parentKey, $iter);
            return $resultLine;
        }, $tree);
        $line = [...$lines];
        $line = array_filter($line, fn($path) => $path !== null);
        return implode("\n", $line);
    };
    $result = $iter($tree);
    return $result;
}

function normalizeArrayValue(array $value, string $type, array $parentKey, callable $iter)
{
    if (is_array($value) && $type !== "parent node") {
        return "[complex value]";
    }
    return $iter($value, $parentKey);
}

function getResultByType(string $type, string $key, $value, array $node, array $parentKey, callable $iter)
{
    $parentKey = implode(".", $parentKey);
    switch ($type) {
        case "parent node":
            return "$value";
        case "changed":
            $value2 = is_array($node["value2"])
            ? normalizeArrayValue(getValue2($node), $type, $parentKey, $iter)
            : normalizeValue(getValue2($node), $type);
            return "Property '$parentKey' was updated. From $value to $value2";
        case "deleted":
            return "Property '$parentKey' was removed";
        case "added":
            return "Property '$parentKey' was added with value: $value";
        default:
            break;
    }
}

function normalizeValue(string $value, string $type)
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
    if (in_array($value, ["'0'", "'1'", "'3'", "'4'", "'5'", "'6'", "'7'", "'8'", "'9'"], true)) {
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
