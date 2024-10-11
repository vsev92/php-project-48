<?php

namespace Gendiff\Diff;
use  function Gendiff\Parser\parseJsonFile;



function getUniqueKeys($jsonCollection1, $jsonCollection2) {
        $keys1 = array_keys($jsonCollection1);
        $keys2 = array_keys($jsonCollection2);
        $commonKeysCollection = array_merge($keys1, $keys2);
        $commonKeysCollection = array_unique($commonKeysCollection, SORT_STRING);
        sort($commonKeysCollection, SORT_STRING);
        return $commonKeysCollection;
         
}

function getDiffByKey($jsonKey, $jsonCollection1,$jsonCollection2) {

        $diff = [];
        $itemExistInCollection1 = array_key_exists($jsonKey,$jsonCollection1);
        $itemExistInCollection2 = array_key_exists($jsonKey,$jsonCollection2);

        if ($itemExistInCollection1 && $itemExistInCollection2) {
                $jsonValue1 = json_encode($jsonCollection1[$jsonKey]); 
                $jsonValue2 = json_encode($jsonCollection2[$jsonKey]); 

                if ($jsonValue1 === $jsonValue2) {
                        $diff[] = "  " .$jsonKey . ":" . $jsonValue1;    
                } else {
                        $diff[] = "-" . " " .  $jsonKey . ":" . $jsonValue1;
                        $diff[] = "+" . " " .  $jsonKey . ":" . $jsonValue2;

                }
        } else {
                if (!$itemExistInCollection1) {
                        $jsonValue2 = json_encode($jsonCollection2[$jsonKey]); 
                        $diff[] = "+" . " " .  $jsonKey  . ":" . $jsonValue2;
                }
                if (!$itemExistInCollection2) {
                        $jsonValue1 = json_encode($jsonCollection1[$jsonKey]); 
                        $diff[] = "-" . " " .  $jsonKey  . ":" . $jsonValue1;
                }


        }
        return $diff;
  
}





function genDiff($pathToFile1, $pathToFile2) {
        $jsonCollection1 = parseJsonFile($pathToFile1);

        $jsonCollection2  = parseJsonFile($pathToFile2);

        $keys =  getUniqueKeys($jsonCollection1, $jsonCollection2);
        
        $diffColl =  array_reduce($keys, function($acc, $key) use ($jsonCollection1, $jsonCollection2) {
                $diff = getDiffByKey($key, $jsonCollection1,$jsonCollection2); 
                $acc = [...$acc, ...$diff];
                return $acc; 
        }, []);

        
        return (implode("\n", $diffColl) . "\n");
}








