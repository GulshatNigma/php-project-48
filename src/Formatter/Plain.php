<?php

namespace Differ\Formatter\Plain;

function getFormat(array $tree)
{
    $iter = function ($tree, $lastKey = "") use (&$iter) {
        $lines = array_map(function ($node) use ($iter, $lastKey) {
            $type = $node["category"];
            $key = $node["key"];
            $parentKey = "$lastKey$key";

            $value = $node["value"];
            $value2 = $node["value2"] ?? null;

            return getResultByType($type, $value, $parentKey, $value2, $iter);
        }, $tree);

        $line = array_filter([...$lines], fn($path) => $path !== null);
        return implode("\n", $line);
    };

    $result = $iter($tree);
    return $result;
}

function normalizeValue(mixed $value, string $parentKey)
{
    if (is_array($value)) {
        return "[complex value]";
    }

    if (gettype($value) === "boolean" || gettype($value) === "integer") {
        return var_export($value, true);
    }

    if ($value === null) {
        return 'null';
    }

    return "'$value'";
}

function getResultByType(string $type, mixed $value, string $parentKey, mixed $value2, callable $iter)
{
    if ($type === "has children") {
        return $iter($value, "$parentKey.");
    }

    $normalizedValue = normalizeValue($value, $parentKey, $iter);
    $normalizedValue2 = normalizeValue($value2, $parentKey, $iter);

    switch ($type) {
        case "changed":
            return "Property '" . $parentKey . "' was updated. From $normalizedValue to $normalizedValue2";
        case "deleted":
            return "Property '" . $parentKey . "' was removed";
        case "added":
            return "Property '" . $parentKey . "' was added with value: $normalizedValue";
        default:
            break;
    }
}
