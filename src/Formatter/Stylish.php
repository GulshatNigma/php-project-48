<?php

namespace Differ\Formatter\Stylish;

function getFormat($tree, $depth = 1)
{
    $iter = function ($tree, $depth) use (&$iter) {
        $indentStart = str_repeat("  ", $depth);
        $indentEnd = str_repeat("  ", $depth - 1);
        $lines = array_map(function ($node) use ($indentStart, $depth, $iter) {
            $type = getCategory($node);
            $key = getKey($node);
            $value = getValue($node);
            if (is_array($value)) {
                $value = $iter($value, $depth + 2);
            }

            if ($type === "changed") {
                $value2 = getValue2($node);
                if ($value === "") {
                    return "$indentStart- $key:" . "\n" . "$indentStart+ $key: $value2";
                }
                return "$indentStart- $key: $value" . "\n" . "$indentStart+ $key: $value2";
            }

            if ($type  === "parent node") {
                return "$indentStart  $key: $value";
            }

            if ($type === "deleted") {
                return "$indentStart- $key: $value";
            }

            if ($type === "added") {
                return "$indentStart+ $key: $value";
            }

            if ($type === "unchanged") {
                return "$indentStart  $key: $value";
            }
        }, $tree);
        $result = ["{", ...$lines, "{$indentEnd}}"];
        return implode("\n", $result);
    };
    $result = $iter($tree, 1);
    return $result . "\n";
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
