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
            [$this->getFullPath('resultStylish.json'),
            $this->getFullPath('file1.json'), $this->getFullPath('file2.json')],

            [$this->getFullPath('resultStylish.json'),
            $this->getFullPath('file1.yaml'), $this->getFullPath('file2.yaml')],

            [$this->getFullPath('resultPlain.json'),
            $this->getFullPath('file1.json'), $this->getFullPath('file2.json'), "plain"],

            [$this->getFullPath('resultPlain.json'),
            $this->getFullPath('file1.yaml'), $this->getFullPath('file2.yaml'), "plain"],

            [$this->getFullPath('resultJson.json'),
            $this->getFullPath('file1.json'), $this->getFullPath('file2.json'), "json"]
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function testStylish($expected, $file1, $file2, $format = "stylish")
    {
        $diff = genDiff($file1, $file2, $format);
        $this->assertStringEqualsFile($expected, $diff);
    }
}
