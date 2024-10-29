<?php

use PHPUnit\Framework\TestCase;
use Differ\Differ;
use Differ\Formatters;
use Differ\Parser\SourceType;

class DiffTest extends TestCase
{



      public function testDiffColl(): void
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

          $diffCol = \Differ\Differ\makeDiffCollection($col1, $col2);
    
         
          $this->assertEquals($expected, $diffCol);

        }

}

