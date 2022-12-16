<?php

namespace Differ\Formatter\Plain;

use function Functional\select_keys;
use function Functional\flatten;

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
            if ($type === "changed") {
                $value2 = is_array($node["value2"])
                ? normalizeArrayValue(getValue2($node), $type, $parentKey, $iter)
                : normalizeValue(getValue2($node), $type);
                return getResultByType($type, $value, $parentKey, $value2);
            }
            $resultLine = getResultByType($type, $value, $parentKey);
            return $resultLine;
        }, $tree);
        $line = array_filter([...$lines], fn($path) => $path !== null);
        return implode("\n", $line);
    };
    $result = $iter($tree);
    return $result;
}

function normalizeArrayValue(array $value, string $type, array $parentKey, callable $iter)
{
    if ($type !== "parent node") {
        return "[complex value]";
    }
    return $iter($value, $parentKey);
}

function getResultByType(string $type, string $value, array $parentKey, string $value2 = "")
{
    switch ($type) {
        case "parent node":
            return "$value";
        case "changed":
            return "Property '" . implode(".", $parentKey) . "' was updated. From $value to $value2";
        case "deleted":
            return "Property '" . implode(".", $parentKey) . "' was removed";
        case "added":
            return "Property '" . implode(".", $parentKey) . "' was added with value: $value";
        default:
            break;
    }
}

function normalizeValue(string $value, string $type)
{
    if ($value === "false" || $value === "true" || $value === "null") {
        return toString($value);
    }
    if (in_array($value, ["0", "1", "3", "4", "5", "6", "7", "8", "9"], true)) {
        return toString($value);
    }
    return "'$value'";
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
