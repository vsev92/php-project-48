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

        $diffCol = \Gendiff\Diff\getDiffByCollection($col1, $col2);

        

        $stylish = \Gendiff\Formatters\getStylishFromDiffCol($diffCol, 1);

        $this->assertEquals($expected, $stylish);

    }

    public function testGetStylishFromColYaml(): void
    {
  
        $expected = file_get_contents(__DIR__ . '/fixtures/TestCompareNestedYaml/expected');
        $path1 = (__DIR__ . '/fixtures/TestCompareNestedYaml/file1.yml');
        $path2 = (__DIR__ . '/fixtures/TestCompareNestedYaml/file2.yml');

        $col1 = \Gendiff\Parser\parseFromFile($path1, \Gendiff\Parser\SourceType::yaml);
        $col2 = \Gendiff\Parser\parseFromFile($path2, \Gendiff\Parser\SourceType::yaml);

        $diffCol = \Gendiff\Diff\getDiffByCollection($col1, $col2);

        

        $stylish = \Gendiff\Formatters\getStylishFromDiffCol($diffCol, 1);

        $this->assertEquals($expected, $stylish);

    }

    public function testGetStylishFromColSimple(): void
    {
  
/*       $expected = file_get_contents(__DIR__ . '/fixtures/TestFormattersGetStylishFromColSimple/expected');
        $path1 = (__DIR__ . '/fixtures/TestFormattersGetStylishFromColSimple/file1.json');
        $path2 = (__DIR__ . '/fixtures/TestFormattersGetStylishFromColSimple/file2.json');

        $col1 = \Gendiff\Parser\parseFromFile($path1, \Gendiff\Parser\SourceType::json);
        $col2 = \Gendiff\Parser\parseFromFile($path2, \Gendiff\Parser\SourceType::json);

        $diffCol = \Gendiff\Diff\getDiffByCollection($col1, $col2);

        

        $stylish = \Gendiff\Formatters\getStylishFromDiffCol($diffCol, 1);

        $this->assertEquals($expected, $stylish);
*/
    }
 
    
}

