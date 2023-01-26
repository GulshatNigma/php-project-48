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
            ['json'],
            ['yaml'],
            ['yml']
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function testDefaultFormat($formatInput)
    {
        $diff = genDiff($this->getFullPath("file1.$formatInput"), $this->getFullPath("file2.$formatInput"));
        $this->assertStringEqualsFile($this->getFullPath('resultStylish.json'), $diff);
    }

    /**
     * @dataProvider additionProvider
     */
    public function testStylishFormat($formatInput)
    {
        $format = "stylish";
        $diff = genDiff($this->getFullPath("file1.$formatInput"), $this->getFullPath("file2.$formatInput"), $format);

        $this->assertStringEqualsFile($this->getFullPath('resultStylish.json'), $diff);
    }

    /**
     * @dataProvider additionProvider
     */
    public function testPlainFormat($formatInput)
    {
        $format = "plain";
        $diff = genDiff($this->getFullPath("file1.$formatInput"), $this->getFullPath("file2.$formatInput"), $format);

        $this->assertStringEqualsFile($this->getFullPath('resultPlain.json'), $diff);
    }

    /**
     * @dataProvider additionProvider
     */
    public function testJsonFormat($formatInput)
    {
        $format = "json";
        $diff = genDiff($this->getFullPath("file1.$formatInput"), $this->getFullPath("file2.$formatInput"), $format);

        $this->assertStringEqualsFile($this->getFullPath('resultJson.json'), $diff);
    }
}
