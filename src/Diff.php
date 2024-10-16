<?php

namespace Gendiff\Diff;
use  Gendiff\Parser;
use  Gendiff\Parser\SourceType;
use  Symfony\Component\Yaml\Yaml;


function getUniqueKeys($Collection1, $Collection2) {
        $keys1 = array_keys($Collection1);
        $keys2 = array_keys($Collection2);
        $commonKeysCollection = array_merge($keys1, $keys2);
        $commonKeysCollection = array_unique($commonKeysCollection, SORT_STRING);
        sort($commonKeysCollection, SORT_STRING);
        return $commonKeysCollection;
         
}

function getValueByKey($collection, $key, SourceType $type) {
        switch ($type) {
                case   SourceType::json:
                   // return json_encode($collection[$key]);
                   return Yaml::dump($collection[$key]);
                case   SourceType::yaml:
                    return Yaml::dump($collection[$key]);
                default:
                    throw new InvalidArgumentException('Unsupported type' . $type);
        }

}




function getDiffByKey($key, $collection1, $collection2, SourceType $type) {

        $diff = [];
 
        
   
        $ExistInCollection1 = array_key_exists($key,$collection1);
        $ExistInCollection2 = array_key_exists($key,$collection2);

        
        if ($ExistInCollection1 && $ExistInCollection2) {
                $value1 = getValueByKey($collection1, $key, $type);
                $value2 = getValueByKey($collection2, $key, $type); 

                if ($value1 === $value2) {
                        $diff[] = "    " . $key . ': ' . $value1;    
                } else {
                        $diff[] = "  -" . " " .  $key . ': ' . $value1;
                        $diff[] = "  +" . " " .  $key . ': ' . $value2;
                }
        } else {
                if (!$ExistInCollection1) {
                        $value2 = getValueByKey($collection2, $key, $type);
                        $diff[] = "  +" . " " .  $key  . ': ' . $value2;
                }
                if (!$ExistInCollection2) {
                        $value1 = getValueByKey($collection1, $key, $type); 
                        $diff[] = "  -" . " " .  $key  . ': ' . $value1;
                }


        }
        return $diff;
  
}



function genDiff($pathToFile1, $pathToFile2) {

        $sourceType1 = \Gendiff\Parser\getSourceType($pathToFile1);
        $sourceType2 = \Gendiff\Parser\getSourceType($pathToFile2);
        if ($sourceType1 != $sourceType2) {
                throw new Exception('Different source files type');
        }
      
        $collection1 = \Gendiff\Parser\parseFromFile($pathToFile1, $sourceType1);
        $collection2  = \Gendiff\Parser\parseFromFile($pathToFile2, $sourceType2);
       
        $keys =  getUniqueKeys($collection1, $collection2);
        
        $diffColl =  array_reduce($keys, function($acc, $key) use ($collection1, $collection2, $sourceType1) {
                $diff = getDiffByKey($key, $collection1,$collection2, $sourceType1); 
                $acc = [...$acc, ...$diff];
                return $acc; 
        }, []);
        $diffColl = ["{", ...$diffColl, "}"];

        
        return (implode("\n", $diffColl) . "\n");
}








