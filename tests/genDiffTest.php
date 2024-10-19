<?php

use PHPUnit\Framework\TestCase;
use Gendiff\Diff;
use Gendiff\Formatters;
use Gendiff\Parser\SourceType;

class genDiffTest extends TestCase
{

  public function testGetDiffByCollection(): void
  {
  /*
      $pathToFile1 =  __DIR__ . '/fixtures/testGetDiffByCollection/file1.json';
      $pathToFile2 =  __DIR__ . '/fixtures/testGetDiffByCollection/file2.json';

      $col1 = \Gendiff\Parser\parseFromFile($pathToFile1, \Gendiff\Parser\SourceType::json);
      $col2 = \Gendiff\Parser\parseFromFile($pathToFile2, \Gendiff\Parser\SourceType::json);
      
      $expected = ['key' => 'common', 'value'=>2];
      $diffCol = Gendiff\Diff\GetDiffByCollection($col1, $col2);
  
      $this->assertEquals($expected ,  $diffCol);
*/
  }

    public function testgenDiffJson(): void
    {
      /*
        $pathToFile1 =  __DIR__ . '/fixtures/TestCompareNestedJson2/file1.json';
        $pathToFile2 =  __DIR__ . '/fixtures/TestCompareNestedJson2/file2.json';
        $expected = file_get_contents(__DIR__ . '/fixtures/TestCompareNestedJson2/expected');

        $diff = Gendiff\Diff\genDiff($pathToFile1, $pathToFile2);
        $this->assertEquals($expected , $diff);
*/
    }

    public function testgenDiffYaml(): void
    {
      /*
        $pathToFile1 =  __DIR__ . '/fixtures/TestCompareNestedYaml/file1.yaml';
        $pathToFile2 =  __DIR__ . '/fixtures/TestCompareNestedYaml/file2.yaml';
        $expected = file_get_contents(__DIR__ . '/fixtures/TestCompareNestedYaml/expected');

        $diff = Gendiff\Diff\genDiff($pathToFile1, $pathToFile2);
        $this->assertEquals($expected , $diff);
     */
    }

    public function testgenDiffUnsupported(): void
    {
      /*
        $pathToFile1 =  __DIR__ . '/fixtures/TestCompareUnsupported/file1.txt';
        $pathToFile2 =  __DIR__ . '/fixtures/TestCompareUnsupported/file2.yml';
        $this->expectException(InvalidArgumentException::class);
        Gendiff\Diff\genDiff($pathToFile1, $pathToFile2);
      */
    }

    public function testParseFromFile(): void
    {
      /*
        $pathToFile1 =  __DIR__ . '/fixtures/TestCompareUnsupported/file123.json';
        $type = \Gendiff\Parser\getSourceType($pathToFile1);
        $this->expectException(InvalidArgumentException::class);
        \Gendiff\Parser\parseFromFile($pathToFile1, $type);


        $pathToFile2 =  __DIR__ . '/fixtures/TestCompareUnsupported/file2.txt';
        $type = \Gendiff\Parser\getSourceType($pathToFile2);
        $this->expectException(InvalidArgumentException::class);
        \Gendiff\Parser\parseFromFile($pathToFile2, $type);

*/
    }



}

