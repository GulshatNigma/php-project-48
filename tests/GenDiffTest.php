<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

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
            ['resultStylish.json', 'file1.yml', 'file2.yml'],
            ['resultPlain.json', 'file1.json', 'file2.json', "plain"],
            ['resultPlain.json', 'file1.yaml', 'file2.yaml', "plain"],
            ['resultPlain.json', 'file1.yml', 'file2.yml', "plain"],
            ['resultJson.json', 'file1.json', 'file2.json', "json"]
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function test($expected, $file1, $file2, $format = "stylish")
    {
        $diff = genDiff($this->getFullPath($file1), $this->getFullPath($file2), $format);
        $this->assertStringEqualsFile($this->getFullPath($expected), $diff);
    }
}
