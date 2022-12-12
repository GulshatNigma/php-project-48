<?php

namespace Differ\Formatter\Json;

function getFormat($tree)
{
    return json_encode($tree, JSON_PRETTY_PRINT);
}
