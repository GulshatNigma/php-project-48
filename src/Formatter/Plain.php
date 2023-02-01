<?php

namespace Differ\Formatter\Plain;

use function Functional\select_keys;
use function Functional\flatten;

function getFormat(array $tree)
{
    $iter = function ($tree, $lastKey = "") use (&$iter) {
        $lines = array_map(function ($node) use ($iter, $lastKey) {
            $type = $node["category"];
            $key = $node["key"];
            $parentKey = "$lastKey$key";

            $value = normalizeValue($node["value"], $type, $parentKey, $iter);

            if ($type === "changed") {
                $value2 = normalizeValue($node["value2"], $type, $parentKey, $iter);

                return getResultByType($type, $value, $parentKey, $value2);
            }

            return getResultByType($type, $value, $parentKey);
        }, $tree);

        $line = array_filter([...$lines], fn($path) => $path !== null);
        return implode("\n", $line);
    };

    $result = $iter($tree);
    return $result;
}

function normalizeValue(mixed $value, string $type, string $parentKey, $iter)
{
    if (is_array($value)) {
        return $type === "has children" ? $iter($value, "$parentKey.") : "[complex value]";
    }

    if (gettype($value) === "boolean" || gettype($value) === "integer") {
        return var_export($value, true);
    }

    if ($value === null) {
        return 'null';
    }

    return "'$value'";
}

function getResultByType(string $type, string $value, string $parentKey, string $value2 = "")
{
    switch ($type) {
        case "has children":
            return "$value";
        case "changed":
            return "Property '" . $parentKey . "' was updated. From $value to $value2";
        case "deleted":
            return "Property '" . $parentKey . "' was removed";
        case "added":
            return "Property '" . $parentKey . "' was added with value: $value";
        default:
            break;
    }
}
