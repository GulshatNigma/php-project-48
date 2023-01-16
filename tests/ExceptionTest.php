<?php

namespace Differ\Tests\Exception;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class ExceptionTest extends TestCase
{
    public function getFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function additionProvider()
    {
        return [
            ['file1.json', 'file2.json', "txt"],
            ['file1.txt', 'file2.txt'],
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function testUnknownFormat($file1, $file2, $format = "stylish")
    {
        $this->expectException('Error');
        var_dump($this->gendiff($this->getFullPath($file1), $this->getFullPath($file2), $format));
        $diff = $this->gendiff($this->getFullPath($file1), $this->getFullPath($file2), $format);
    }
}
