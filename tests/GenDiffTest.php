<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function Differ\Differ\getDesiredFormat;

class GenDiffTest extends TestCase
{
    public function getFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function additionProvider()
    {
        return [
            ['resultStylish.json', 'file1.json', 'file2.json'],
            ['resultStylish.json', 'file1.yaml', 'file2.yaml'],
            ['resultPlain.json', 'file1.json', 'file2.json', "plain"],
            ['resultPlain.json', 'file1.yaml', 'file2.yaml', "plain"],
            ['resultJson.json', 'file1.json', 'file2.json', "json"]
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function testStylish($expected, $file1, $file2, $format = "stylish")
    {
        $diff = genDiff($this->getFullPath($file1), $this->getFullPath($file2), $format);
        $this->assertStringEqualsFile($this->getFullPath($expected), $diff);
    }
}
