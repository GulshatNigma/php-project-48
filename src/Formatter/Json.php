<?php

namespace Differ\Formatter\Json;

function getFormat(array $tree): string
{
    return json_encode($tree, JSON_PRETTY_PRINT);
}
