<?php

namespace Differ\Formatter\Stylish;

use Exception;

function getFormat(array $tree)
{
    $iter = function ($tree, $depth = 1) use (&$iter) {
        $indentStart = str_repeat("  ", $depth);
        $indentEnd = str_repeat("  ", $depth - 1);

        $lines = array_map(function ($node) use ($indentStart, $depth, $iter) {
            $type = $node["category"];
            $key = $node["key"];
            $value = normalizeValue($node["value"], $type, $depth, $iter);

            if ($type === "changed") {
                $value2 = normalizeValue($node["value2"], $type, $depth, $iter);

                return getResultByType($type, $indentStart, $key, $value, $value2);
            }

            return getResultByType($type, $indentStart, $key, $value);
        }, $tree);

        $result = ["{", ...$lines, "{$indentEnd}}"];
        return implode("\n", $result);
    };

    $line = $iter($tree, 1);
    return $line;
}

function getResultByType(string $type, string $indentStart, string $key, mixed $value, mixed $value2 = "")
{
    switch ($type) {
        case "changed":
            return "$indentStart- $key: $value" . "\n" . "$indentStart+ $key: $value2";
        case "unchanged":
        case "has children":
            return "$indentStart  $key: $value";
        case "deleted":
            return "$indentStart- $key: $value";
        case "added":
            return "$indentStart+ $key: $value";
        default:
            throw new Exception("Unknown type");
    }
}

function normalizeValue(mixed $value, string $type, int $depth, callable $iter)
{
    if (is_array($value)) {
        return $iter($value, $depth + 2);
    }

    if (gettype($value) === "boolean") {
        return var_export($value, true);
    }

    if ($value === null) {
        return 'null';
    }

    return $value;
}
