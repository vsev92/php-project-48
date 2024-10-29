<?php

use PHPUnit\Framework\TestCase;
use Gendiff\Parser\SourceType;

class ParsersTest extends TestCase
{

  public function testParserGetSourceType(): void
  {
  
      $pathToJsonFile1 =  __DIR__ . '/fixtures/file1.json';
      $pathToYmlFile1 =  __DIR__ . '/fixtures/file1.yml';
      $pathToYamlFile1 =  __DIR__ . '/fixtures/file1.yaml';

      


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
    
        $pathToJsonFile1 =  __DIR__ . '/fixtures/file1.json';
     
        $pathToYmlFile1 =  __DIR__ . '/fixtures/file1.yml';
     
        $pathToYamlFile1 =  __DIR__ . '/fixtures/file1.yaml';

        $pathToExpected =  __DIR__ . '/fixtures/ExpectedCol1';
        
        $wrongPath =  __DIR__ . '/fixtures/file1.qwe';
        
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



}

