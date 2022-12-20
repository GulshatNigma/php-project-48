<?php

namespace Differ\DesiredFormat;

use Exception;

use function Differ\Formatter\Stylish\getFormat as getFormatStylish;
use function Differ\Formatter\Plain\getFormat as getFormatPlain;
use function Differ\Formatter\Json\getFormat as getFormatJson;

function getDesiredFormat(string $format, array $differenceTree)
{
    switch ($format) {
        case "plain":
            return getFormatPlain($differenceTree);
        case "json":
            return getFormatJson($differenceTree);
        case "stylish":
            return getFormatStylish($differenceTree);
        default:
            throw new Exception("Unknown format");
    }
}
