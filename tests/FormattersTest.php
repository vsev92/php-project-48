<?php

use PHPUnit\Framework\TestCase;
use Gendiff\Parser;
use Gendiff\Formatters;


class FormattersTest extends TestCase
{

    public function testGetStylishFromCol(): void
    {
  
        $expected = file_get_contents(__DIR__ . '/fixtures/TestFormattersGetStylishFromCol/expected');
        $path1 = (__DIR__ . '/fixtures/TestFormattersGetStylishFromCol/file1.json');
        $path2 = (__DIR__ . '/fixtures/TestFormattersGetStylishFromCol/file2.json');

        $col1 = \Gendiff\Parser\parseFromFile($path1, \Gendiff\Parser\SourceType::json);
        $col2 = \Gendiff\Parser\parseFromFile($path2, \Gendiff\Parser\SourceType::json);

        $diffCol = \Gendiff\Diff\getDiffByCollection($col1, $col2);

        

        $stylish = \Gendiff\Formatters\getStylishFromDiffCol($diffCol, 1);

        $this->assertEquals($expected, $stylish);

    }
 
    
}

