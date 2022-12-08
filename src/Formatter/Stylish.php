<?php

namespace Differ\Formatter\Stylish;

function getFormatStylish($tree, $depth = 1)
{
    $iter = function ($tree, $depth) use (&$iter) {
        $identStart = str_repeat("  ", $depth);
        $identEnd = str_repeat("  ", $depth - 1);
        $lines = array_map(function ($node) use ($identStart, $depth, $identEnd, $iter) {
            $type = getCategory($node);
            $key = getKey($node);
            $value = getValue($node);
            if (is_array($value)) {
                $value = $iter($value, $depth + 2);
            }

            if ($type === "changed") {
                $value2 = getValue2($node);
                if ($value === "") {
                    return "$identStart- $key:" . "\n" . "$identStart+ $key: $value2";
                }
                if ($value2 === "") {
                    return "$identStart- $key:" . "\n" . "$identStart+ $key:";
                }
                return "$identStart- $key: $value" . "\n" . "$identStart+ $key: $value2";
            }
            if ($type  === "parent node") {
                return "$identStart  $key: $value";
            }

            if ($type === "deleted") {
                if ($value === "") {
                    return "$identStart- $key:";
                }
                return "$identStart- $key: $value";
            }

            if ($type === "added") {
                if ($value === "") {
                    return "$identStart+ $key:";
                }
                return "$identStart+ $key: $value";
            }

            if ($type === "unchanged") {
                if ($value === "") {
                    return "$identStart  $key:";
                }
                return "$identStart  $key: $value";
            }
        }, $tree);
        $result = ["{", ...$lines, "{$identEnd}}"];
        return implode("\n", $result);
    };
    $result = $iter($tree, 1);
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

function toString($value)
{
    return trim(var_export($value, true), "'");
}
