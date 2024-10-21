<?php

namespace Gendiff\Diff;
use  Gendiff\Formatters;
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






function getDiffByKey($key, $collection1, $collection2) {

        $diff = [];
 
        
   
        $ExistInCollection1 = array_key_exists($key,$collection1);
        $ExistInCollection2 = array_key_exists($key,$collection2);
        $ValueIsArray1 = is_array($collection1[$key]);
        $ValueIsArray2 = is_array($collection2[$key]);

        
        if ($ExistInCollection1 && $ExistInCollection2) {
                if ($ValueIsArray1 && $ValueIsArray2)
                {
                        $diffByColl = getDiffByCollection($collection1[$key], $collection2[$key]);
                        $diff [] = ['sign' => '  ', 'key' => $key, 'value'=> $diffByColl]; 

                } elseif (!$ValueIsArray1 && !$ValueIsArray2) {
                        if ($collection1[$key] === $collection2[$key]) {
                                $diff [] = ['sign' => '  ', 'key' => $key, 'value'=> $collection1[$key]]; 
                        } else {
                                $diff [] = ['sign' => '- ', 'key' => $key, 'value'=> $collection1[$key]];
                                $diff [] = ['sign' => '+ ', 'key' => $key, 'value'=> $collection2[$key]];  
                        }
                } else {
                        $value1 = $ValueIsArray1 ? getDiffColFromAssoc($collection1[$key]) : $collection1[$key];
                        $value2 = $ValueIsArray2 ? getDiffColFromAssoc($collection2[$key]) : $collection2[$key];
                        $diff [] = ['sign' => '- ', 'key' => $key, 'value'=> $value1];
                        $diff [] = ['sign' => '+ ', 'key' => $key, 'value'=> $value2]; 
                }
                
        } else {
                if (!$ExistInCollection1) {
                        if (is_array($collection2[$key])) {
                                $value = getDiffColFromAssoc($collection2[$key]);
                                $diff [] = ['sign' => '+ ', 'key' => $key, 'value'=> $value];
                        } else {
                                $diff [] = ['sign' => '+ ', 'key' => $key, 'value'=> $collection2[$key]];
                        }       
                }
                if (!$ExistInCollection2) {
                        if (is_array($collection1[$key])) {
                                $value = getDiffColFromAssoc($collection1[$key]);
                                $diff [] = ['sign' => '- ', 'key' => $key, 'value'=> $value];
                        } else {
                                $diff [] = ['sign' => '- ', 'key' => $key, 'value'=> $collection1[$key]];
                        }
                        
                }


        }
     
        return $diff;
  
}

function getDiffColFromAssoc($array) {
        $keys = array_keys($array);
        return  array_map(function($key) use ($array) {
                $value = is_array($array[$key]) ? getDiffColFromAssoc($array[$key]) : $array[$key];
                return ['sign' => '  ', 'key' => $key, 'value'=> $value];
                
        },$keys);
        
}


function getDiffByCollection($collection1, $collection2)
{
        $keys =  getUniqueKeys($collection1, $collection2);
        $diffColl =  array_reduce($keys, function($acc, $key) use ($collection1, $collection2) {
                $diff = getDiffByKey($key, $collection1,$collection2); 
                $acc = [...$acc, ...$diff];
                return $acc; 
        }, []);
        return $diffColl;

}

function genDiff($pathToFile1, $pathToFile2) {

        $sourceType1 = \Gendiff\Parser\getSourceType($pathToFile1);
        $sourceType2 = \Gendiff\Parser\getSourceType($pathToFile2);

      
        $collection1 = \Gendiff\Parser\parseFromFile($pathToFile1, $sourceType1);
        $collection2  = \Gendiff\Parser\parseFromFile($pathToFile2, $sourceType2);
       
        

        $diffByColl = getDiffByCollection($collection1, $collection2);
        
        
        
        return \Gendiff\Formatters\getStylishFormat(true, '', $diffByColl, 0, 0, 2);
}                                  










