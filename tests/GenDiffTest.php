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

    public function testJson1()
    {
        $file1 = $this->getFixtureFullPath('File1.json');
        $file2 = $this->getFixtureFullPath('File2.json');
        $result = file_get_contents($this->getFixtureFullPath('ResultJsonfiles.json'));

        $diff = genDiff($file1, $file2);
        return $this->assertEquals($result, $diff);
    }
    
    public function testJson2()
    {
        $file1 = $this->getFixtureFullPath('file1.json');
        $file2 = $this->getFixtureFullPath('file2.json');
        $result = file_get_contents($this->getFixtureFullPath('resultJsonFiles.json'));

        $diff = genDiff($file1, $file2);
        return $this->assertEquals($diff, $result);
    }

    public function testYaml()
    {
        $file1 = $this->getFixtureFullPath('file1.yaml');
        $file2 = $this->getFixtureFullPath('file2.yml');
        $result = file_get_contents($this->getFixtureFullPath('resultYamlFiles.yaml'));

        $diff = genDiff($file1, $file2);
        var_dump($result);
        return $this->asserEquals(genDiff($file1, $file2), $result);
    }
}
