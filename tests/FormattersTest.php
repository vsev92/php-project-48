<?php

use PHPUnit\Framework\TestCase;
use Gendiff\Parser;
use Gendiff\Formatters;


class FormattersTest extends TestCase
{


    public function testGetStylishFromColJson(): void
    {
  
        $expected = file_get_contents(__DIR__ . '/fixtures/TestCompareNestedJson/expected');
        $path1 = (__DIR__ . '/fixtures/TestCompareNestedJson/file1.json');
        $path2 = (__DIR__ . '/fixtures/TestCompareNestedJson/file2.json');

        $col1 = \Gendiff\Parser\parseFromFile($path1, \Gendiff\Parser\SourceType::json);
        $col2 = \Gendiff\Parser\parseFromFile($path2, \Gendiff\Parser\SourceType::json);

        $diffCol = \Gendiff\Diff\makeDiffCollection($col1, $col2);

        

        $stylish = \Gendiff\Formatters\getFormattedDiffCol($diffCol, 'stylish');

        $this->assertEquals($expected, $stylish);

    }

    public function testGetStylishFromColYaml(): void
    {
  
        $expected = file_get_contents(__DIR__ . '/fixtures/TestCompareNestedYaml/expected');
        $path1 = (__DIR__ . '/fixtures/TestCompareNestedYaml/file1.yml');
        $path2 = (__DIR__ . '/fixtures/TestCompareNestedYaml/file2.yml');

        $col1 = \Gendiff\Parser\parseFromFile($path1, \Gendiff\Parser\SourceType::yaml);
        $col2 = \Gendiff\Parser\parseFromFile($path2, \Gendiff\Parser\SourceType::yaml);

        $diffCol = \Gendiff\Diff\makeDiffCollection($col1, $col2);

        

        $stylish = \Gendiff\Formatters\getFormattedDiffCol($diffCol, 'stylish');

        $this->assertEquals($expected, $stylish);

    }

    public function testPlainFormatter(): void
    {
  
        $expected = file_get_contents(__DIR__ . '/fixtures/TestPlainFormatter/expected');
        $path1 = (__DIR__ . '/fixtures/TestPlainFormatter/file1.json');
        $path2 = (__DIR__ . '/fixtures/TestPlainFormatter/file2.json');

        $col1 = \Gendiff\Parser\parseFromFile($path1, \Gendiff\Parser\SourceType::json);
        $col2 = \Gendiff\Parser\parseFromFile($path2, \Gendiff\Parser\SourceType::json);

        $diffCol = \Gendiff\Diff\makeDiffCollection($col1, $col2);

        

        $stylish = \Gendiff\Formatters\getFormattedDiffCol($diffCol, 'plain');
  
        $this->assertEquals($expected, $stylish);

    
    }

    public function testJsonFormatter(): void
    {
  
        $expected = file_get_contents(__DIR__ . '/fixtures/TestJsonFormatter/expected');
        $path1 = (__DIR__ . '/fixtures/TestJsonFormatter/file1.json');
        $path2 = (__DIR__ . '/fixtures/TestJsonFormatter/file2.json');

        $col1 = \Gendiff\Parser\parseFromFile($path1, \Gendiff\Parser\SourceType::json);
        $col2 = \Gendiff\Parser\parseFromFile($path2, \Gendiff\Parser\SourceType::json);

        $diffCol = \Gendiff\Diff\makeDiffCollection($col1, $col2);

        

        $stylish = \Gendiff\Formatters\jsonDumpDiffCol($diffCol);

        $this->assertEquals($expected, $stylish);

    
    }
 
}

