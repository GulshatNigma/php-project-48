<?php
namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class gendiffTest extends TestCase
{
    public function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function test()
    {
        $file1 = $this->getFixtureFullPath('file1.json');
        $file2 = $this->getFixtureFullPath('file2.json');
        $result = file_get_contents($this->getFixtureFullPath('resultJsonFiles.json'));

        $diff = genDiff($file1, $file2);
        return $this->assertTrue(genDiff($file1, $file2) == $result);
    }
}
