<?php

namespace Differ\Formatter\Json;

function getFormatJson($tree)
{
    return json_encode($tree, JSON_PRETTY_PRINT);
}