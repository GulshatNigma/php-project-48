<?php

namespace Differ\Cli;

use Docopt;

use function Differ\Differ\genDiff;

function runCli()
{
    $doc = <<<'DOCOPT'
    gendiff -h

    Generate diff

    Usage:
        gendiff (-h|--help)
        gendiff (-v|--version)
        gendiff [--format <fmt>] <firstFile> <secondFile>

    Options:
      -h --help                Show this screen
      -v --version             Show version
      --format <fmt>           Report format [default: stylish]
    DOCOPT;

    $args = Docopt::handle($doc, array('version' => '1.0'));

    $pathToFile1 = $args['<firstFile>'];
    $pathToFile2 = $args['<secondFile>'];
    $format = $args['--format'];

    $difference = genDiff($pathToFile1, $pathToFile2, $format);
    echo($difference);
}
