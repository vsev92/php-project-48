<?php

use PHPUnit\Framework\TestCase;
use Gendiff\Diff;
use Gendiff\Formatters;
use Gendiff\Parser\SourceType;

class genDiffTest extends TestCase
{

  public function testParserGetSourceType(): void
  {
  
      $pathToJsonFile1 =  __DIR__ . '/fixtures/TestParser/file1.json';
      $pathToJsonFile2 =  __DIR__ . '/fixtures/TestParser/file2.json';
      $pathToYmlFile1 =  __DIR__ . '/fixtures/TestParser/file1.yml';
      $pathToYmlFile2 =  __DIR__ . '/fixtures/TestParser/file2.yml';
      $pathToYamlFile1 =  __DIR__ . '/fixtures/TestParser/file1.yaml';
      $pathToYamlFile2 =  __DIR__ . '/fixtures/TestParser/file2.yaml';
      


      $type1 = \Gendiff\Parser\getSourceType($pathToJsonFile1);
      $this->assertEquals(SourceType::json, $type1);

      $type2 = \Gendiff\Parser\getSourceType($pathToYmlFile1);
      $this->assertEquals(SourceType::yaml, $type2);



      $type3 = \Gendiff\Parser\getSourceType($pathToYamlFile1);
      $this->assertEquals(SourceType::yaml, $type3);
      
      $this->expectException(Exception::class);
      $type4 = \Gendiff\Parser\getSourceType('');
  
      



    }

    public function testParserParseFromFile(): void
    {
    
        $pathToJsonFile1 =  __DIR__ . '/fixtures/TestParser/file1.json';
        $pathToJsonFile2 =  __DIR__ . '/fixtures/TestParser/file2.json';
        $pathToYmlFile1 =  __DIR__ . '/fixtures/TestParser/file1.yml';
        $pathToYmlFile2 =  __DIR__ . '/fixtures/TestParser/file2.yml';
        $pathToYamlFile1 =  __DIR__ . '/fixtures/TestParser/file1.yaml';
        $pathToYamlFile2 =  __DIR__ . '/fixtures/TestParser/file2.yaml';
        $pathToExpected =  __DIR__ . '/fixtures/TestParser/ExpectedCol1';
        $wrongPath =  __DIR__ . '/fixtures/TestParser/file1.qwe';
        
        $expected = file_get_contents($pathToExpected);
        $expected = unserialize($expected);
  
        $parsed1 = \Gendiff\Parser\parseFromFile($pathToJsonFile1, SourceType::json);
        $this->assertEquals($expected, $parsed1);

        $parsed2 = \Gendiff\Parser\parseFromFile($pathToYmlFile1, SourceType::yaml);
        $this->assertEquals($expected, $parsed2);

        $parsed3 = \Gendiff\Parser\parseFromFile($pathToYamlFile1, SourceType::yaml);
        $this->assertEquals($expected, $parsed3);

        $this->expectException(InvalidArgumentException::class);
        $parsed3 = \Gendiff\Parser\parseFromFile($wrongPath, null);

        $this->expectException(InvalidArgumentException::class);
        $parsed3 = \Gendiff\Parser\parseFromFile($pathToYamlFile1, null);
  

  
      }

      public function testGenDiffColl(): void
      {
      
          $pathToCol1 =  __DIR__ . '/fixtures/Col1';
          $pathToCol2 =  __DIR__ . '/fixtures/Col2';
          $pathToExpected =  __DIR__ . '/fixtures/DiffCol';
          
          $expected = file_get_contents($pathToExpected);
          $expected = unserialize($expected);

          $col1 = file_get_contents($pathToCol1);
          $col1 = unserialize($col1);

          $col2 = file_get_contents($pathToCol2);
          $col2 = unserialize($col2);

          $diffCol = \Gendiff\Diff\makeDiffCollection($col1, $col2);
    
         
          $this->assertEquals($expected, $diffCol);

        }

}

