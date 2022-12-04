<?php

namespace Differ\Formatter\Stylish;

function getFormatStylish($tree)
{
    $result = array_map(function ($node) {
        $type = getCategory($node);
        $key = getKey($node);
        
        if ($type === "changed") {
            $value1 = getValue1($node);
            $value2 = getValue2($node);
            return "- $key: $value1" . "\n" . "+ $key: $value2";
        }

        $value = getValue($node);

        if ($type  === "parent node") {
            if (is_array($value)) {
                $value = getFormatStylish($value);
            }
            return "  $key: $value";
        }

        if ($type === "deleted") {
            return "- $key: $value";
        }

        if ($type === "added") {
            return "+ $key: $value";
        }

        if ($type === "unchanged") {
            return "  $key: $value";
        }

    }, $tree);
    $result = implode("\n", $result);
    return "{\n$result\n}\n";
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

function getValue1($node)
{
    return $node["value1"];
}

function getValue2($node)
{
    return $node["value2"];
}
