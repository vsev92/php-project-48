<?php

use PHPUnit\Framework\TestCase;
use Gendiff\Diff;

class genDiffTest extends TestCase
{
    public function test1genDiff(): void
    {
      
        $pathToFile1 =  __DIR__ . '/fixtures/file1Test1.json';
        $pathToFile2 =  __DIR__ . '/fixtures/file2Test1.json';
        $expected = file_get_contents(__DIR__ . '/fixtures/expectedTest1');

        $diff = Gendiff\Diff\genDiff($pathToFile1, $pathToFile2);
        $this->assertEquals($expected , $diff);

    }
}

