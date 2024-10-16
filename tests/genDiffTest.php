<?php

use PHPUnit\Framework\TestCase;
use Gendiff\Diff;
use Gendiff\Parser\SourceType;

class genDiffTest extends TestCase
{
    public function testgenDiffJson(): void
    {
      
        $pathToFile1 =  __DIR__ . '/fixtures/TestCompareJson/file1.json';
        $pathToFile2 =  __DIR__ . '/fixtures/TestCompareJson/file2.json';
        $expected = file_get_contents(__DIR__ . '/fixtures/TestCompareJson/expected');

        $diff = Gendiff\Diff\genDiff($pathToFile1, $pathToFile2);
        $this->assertEquals($expected , $diff);

    }

    public function testgenDiffYaml(): void
    {
      
        $pathToFile1 =  __DIR__ . '/fixtures/TestCompareYaml/file1.yml';
        $pathToFile2 =  __DIR__ . '/fixtures/TestCompareYaml/file2.yml';
        $expected = file_get_contents(__DIR__ . '/fixtures/TestCompareYaml/expected');

        $diff = Gendiff\Diff\genDiff($pathToFile1, $pathToFile2);
        $this->assertEquals($expected , $diff);

    }

    public function testgenDiffUnsupported(): void
    {
      
        $pathToFile1 =  __DIR__ . '/fixtures/TestCompareUnsupported/file1.txt';
        $pathToFile2 =  __DIR__ . '/fixtures/TestCompareUnsupported/file2.yml';
        $this->expectException(Error::class);
        Gendiff\Diff\genDiff($pathToFile1, $pathToFile2);

    }

    public function testParseFromFile(): void
    {
      
        $pathToFile1 =  __DIR__ . '/fixtures/TestCompareUnsupported/file123.json';
        $type = \Gendiff\Parser\getSourceType($pathToFile1);
        $this->expectException(InvalidArgumentException::class);
        \Gendiff\Parser\parseFromFile($pathToFile1, $type);


        $pathToFile2 =  __DIR__ . '/fixtures/TestCompareUnsupported/file2.txt';
        $type = \Gendiff\Parser\getSourceType($pathToFile2);
        $this->expectException(InvalidArgumentException::class);
        \Gendiff\Parser\parseFromFile($pathToFile2, $type);


    }



    
}

