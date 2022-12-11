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
            $value = getNormalValue($value, $type);
            switch ($type) {
                case "parent node":
                    if (is_array($value)) {
                        $lastKey .= "$key.";
                        $value = $iter($value, $lastKey);
                    }
                    return "$value";
                case "changed":
                    $value2 = getValue2($node);
                    $value2 = getNormalValue($value2, $type);
                    return "Property '$string$key' was updated. From $value to $value2";
                case "deleted":
                    return "Property '$string$key' was removed";
                case "added":
                    return "Property '$string$key' was added with value: $value";
                case "unchanged":
                    break;
            }
        }, $tree);
        $line = [...$lines];
        $line = array_filter($line, fn($string) => $string !== null);
        return implode("\n", $line);
    };
    $result = $iter($tree);
    return $result;
}

function getNormalValue($value, $type)
{
    $value = gettype($value) === 'string' ? "'$value'" : $value;
    $value = is_array($value) && $type !== "parent node" ? "[complex value]": $value;
    if ($value === "'false'") {
        $value = "false";
    } elseif ($value === "'true'") {
        $value = "true";
    } elseif ($value === "'null'") {
        $value = "null";
    }
    return $value;
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