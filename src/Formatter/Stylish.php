<?php

namespace Differ\Formatter\Stylish;

use Exception;

function getFormat(array $tree)
{
    $iter = function ($tree, $depth = 1) use (&$iter) {
        $indentStart = str_repeat("  ", $depth);
        $indentEnd = str_repeat("  ", $depth - 1);

        $lines = array_map(function ($node) use ($indentStart, $depth, $iter) {
            $type = getCategory($node);
            $key = getKey($node);
            $value = is_array($node["value"]) ? $iter($node["value"], $depth + 2) : getValue($node);
            if ($type === "changed") {
                $value2 = is_array($node["value2"])
                ? $iter($node["value2"], $depth + 2)
                : getValue2($node);

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

function getResultByType(string $type, string $indentStart, string $key, string $value, string $value2 = "")
{
    switch ($type) {
        case "changed":
            $result = "$indentStart- $key: $value" . "\n" . "$indentStart+ $key: $value2";
            break;
        case "has children":
            $result = "$indentStart  $key: $value";
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
            throw new Exception("Unknown type");
    }
    return $result;
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
