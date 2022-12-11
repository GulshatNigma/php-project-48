<?php

namespace Differ\Formatter\Plain;

function getFormatPlain($tree)
{
    $iter = function ($tree, $lastKey = "") use (&$iter) {
        $lines = array_map(function ($node) use ($iter, $lastKey) {
            $string = "";
            if ($lastKey !== "") {
                $string .= "$lastKey" ;
            }
            $type = getCategory($node);
            $key = getKey($node);
            $value = getValue($node);
            if ($type  === "parent node") {
                if (is_array($value)) {
                    $lastKey .= "$key.";
                    $value = $iter($value, $lastKey);
                }
                return "$value";
            }
            $value = is_array($value) ? "[complex value]": "'$value'";

            if ($type === "changed") {
                $value2 = getValue2($node);
                $value2 = is_array($value2) ? "[complex value]": "'$value2'";
                return "Property '$string$key' was updated. From $value to $value2";
            }

            if ($type === "deleted") {
                return "Property '$string$key' was removed";
            }

            if ($type === "added") {

                return "Property '$string$key' was added with value: $value";
            }

            if ($type === "unchanged") {
            }
        }, $tree);
        $line = [...$lines];
        return implode("\n", $line);
    };
    $result = $iter($tree);
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