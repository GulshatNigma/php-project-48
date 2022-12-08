<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function testJsonStylish()
    {
        $file1 = $this->getFixtureFullPath('file1.json');
        $file2 = $this->getFixtureFullPath('file2.json');
        $result = file_get_contents($this->getFixtureFullPath('resultJsonFilesStylish.json'));

        $diff = genDiff($file1, $file2);
        return $this->assertEquals($result, $diff);
    }

    public function testYamlStylish()
    {
        $file1 = $this->getFixtureFullPath('file1.yaml');
        $file2 = $this->getFixtureFullPath('file2.yaml');
        $result = file_get_contents($this->getFixtureFullPath('resultYamlFilesStylish.yaml'));

        $diff = genDiff($file1, $file2);
        return $this->assertEquals($result, $diff);
    }

    public function testJsonPlain()
    {
        $file1 = $this->getFixtureFullPath('file1.json');
        $file2 = $this->getFixtureFullPath('file2.json');
        $result = file_get_contents($this->getFixtureFullPath('resultJsonFilesPlain.json'));

        $format = "plain";
        $diff = genDiff($file1, $file2, $format);
        return $this->assertEquals($result, $diff);
    }
}
