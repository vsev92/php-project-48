<?php

use PHPUnit\Framework\TestCase;
use Gendiff\Diff;

class genDiffTest extends TestCase
{
    public function test1genDiffJson(): void
    {
      
        $pathToFile1 =  __DIR__ . '/fixtures/TestCompareJson/file1.json';
        $pathToFile2 =  __DIR__ . '/fixtures/TestCompareJson/file2.json';
        $expected = file_get_contents(__DIR__ . '/fixtures/TestCompareJson/expected');

        $diff = Gendiff\Diff\genDiff($pathToFile1, $pathToFile2);
        $this->assertEquals($expected , $diff);

    }

    public function test1genDiffYaml(): void
    {
      
        $pathToFile1 =  __DIR__ . '/fixtures/TestCompareYaml/file1.yml';
        $pathToFile2 =  __DIR__ . '/fixtures/TestCompareYaml/file2.yml';
        $expected = file_get_contents(__DIR__ . '/fixtures/TestCompareYaml/expected');

        $diff = Gendiff\Diff\genDiff($pathToFile1, $pathToFile2);
        $this->assertEquals($expected , $diff);

    }
}

