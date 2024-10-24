<?php

namespace Gendiff\Diff;
use  Gendiff\Formatters;
use  Gendiff\Parser;
use  Gendiff\Parser\SourceType;
use  Symfony\Component\Yaml\Yaml;
use  Exception;


function genDiff($pathToFile1, $pathToFile2, $formatName) {

        $sourceType1 = \Gendiff\Parser\getSourceType($pathToFile1);
        $sourceType2 = \Gendiff\Parser\getSourceType($pathToFile2);

      
        $collection1 = \Gendiff\Parser\parseFromFile($pathToFile1, $sourceType1);
        $collection2  = \Gendiff\Parser\parseFromFile($pathToFile2, $sourceType2);
       
        

        $diffCol = getDiffByCollection($collection1, $collection2);
        
        
        
        return \Gendiff\Formatters\getFormattedDifference($diffCol, $formatName);
} 







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
        $ValueIsArray1 = $ExistInCollection1 && is_array($collection1[$key]);
        $ValueIsArray2 = $ExistInCollection2 && is_array($collection2[$key]);

        
        if ($ExistInCollection1 && $ExistInCollection2) {
                
                    $diff = getDiffForTwoValues($key, $collection1[$key], $collection2[$key]);
                
        } else {
                if (!$ExistInCollection1) {
                       
                        $diff = getDiffForOnlyRightExistValue($key, $collection2[$key]);  
                }
                if (!$ExistInCollection2) {

                        $diff = getDiffForOnlyLeftExistValue($key, $collection1[$key]) ;
                        
                }


        }
     
        return $diff;
  
}


function getDiffForTwoValues($key, $value1, $value2)
{
        $ValueIsArray1 = is_array($value1);
        $ValueIsArray2 = is_array($value2);
        if ($ValueIsArray1 && $ValueIsArray2)
        {
                $diffByColl = getDiffByCollection($value1, $value2);
                $diff  = [
                            'key' => $key,
                            'Child'=> $diffByColl, 

                        ]; 

        } elseif (!$ValueIsArray1 && !$ValueIsArray2) {
               
                $diff  = [
                        'key' => $key,
                        'valueInFirst' => $value1,
                        'valueInSecond'=> $value2,
                        'firstValueIsComplex' => false,
                        'secondValueIsComplex' => false

                ]; 
   
        } else {
                $diff  = ['key' => $key,
                          'valueInFirst'=> $value1,
                          'valueInSecond'=> $value2,
                          'firstValueIsComplex' => $ValueIsArray1,
                          'secondValueIsComplex' => $ValueIsArray2
                        ];
        }
        return $diff;

}

function getDiffForOnlyLeftExistValue($key, $leftValue)
{
        $ValueIsArray = is_array($leftValue);
        $diff  = [
                   'key' => $key,
                   'valueInFirst'=> $leftValue,
                   'firstValueIsComplex' => $ValueIsArray
                ];  
        return $diff;   
}

function getDiffForOnlyRightExistValue($key, $rightValue)
{
        $valueIsArray = is_array($rightValue);
              
        $diff = [
                'key' => $key, 
                'valueInSecond'=> $rightValue,
                'secondValueIsComplex' => $valueIsArray
        ];
        return $diff;
}


/// functions for diff processing
enum PropertyDifference
{
    case added;
    case updated;
    case removed;
    case none;
    case complexDifference;
}

function getKey($diff)
{
       return $diff['key']; 
}

function hasChild($diff)
{
       return array_key_exists('Child', $diff); 
}
function getChild($diff)
{
       $child =  hasChild($diff) ? $diff['Child'] : throw new Exception("no child in diff");
       return $child;
}


function isKeyExistsInFirst($diff)
{
       return  array_key_exists('valueInFirst', $diff);
}

function getFirstValue($diff)
{
  
       if (isKeyExistsInFirst($diff)) {
            return $diff['valueInFirst'];
       } else {
            throw new Exception("Value by key " . $diff['key'] . " not found in first collection");
       }
       
}

function isKeyExistsInSecond($diff)
{
        return  array_key_exists('valueInSecond', $diff);
}

function getSecondValue($diff)
{
       $value = isKeyExistsInSecond($diff) ? $diff['valueInSecond'] : null;
       return $value;
}

function isKeyExistsBoth($diff)
{
       return  isKeyExistsInFirst($diff) && isKeyExistsInSecond($diff);
}

function isValuesIdentity($diff)
{
       return   isKeyExistsBoth($diff) && !isFirstValueComplex($diff) && !isSecondValueComplex($diff) && (getFirstValue($diff) === getSecondValue($diff));
}

function isFirstValueComplex($diff)
{
       return  $diff['firstValueIsComplex'];         
}

function isSecondValueComplex($diff)
{
       return  $diff['secondValueIsComplex'];         
}



function isComplexDiffUpdated($diff)
{

        if (hasChild($diff)) {
                $child = getChild($diff);
                $updated = array_filter($child, fn($item)=> isComplexDiffUpdated($child));
                return !is_null($updated);
        } else {
                $difference = getPropertyDifference($diff);
                return $difference !== PropertyDifference::none;

        }

}
      



function getPropertyDifference($diff)
{    
     if (hasChild($diff) ) {
        return PropertyDifference::complexDifference;
     } 
     if (isValuesIdentity($diff) ) {
        return PropertyDifference::none;
     } 

     if (isKeyExistsBoth($diff) &&  !isValuesIdentity($diff)) {
        return PropertyDifference::updated;
     }
      
     if (isKeyExistsInFirst($diff) && !isKeyExistsInSecond($diff)) {
        return PropertyDifference::removed;
     }

     if (!isKeyExistsInFirst($diff) && isKeyExistsInSecond($diff)) {
        return PropertyDifference::added;
     }

     
}

////

function getDiffByCollection($collection1, $collection2)
{
        $keys =  getUniqueKeys($collection1, $collection2);
        $diffColl =  array_reduce($keys, function($acc, $key) use ($collection1, $collection2) {
                $diff = getDiffByKey($key, $collection1,$collection2); 
                $acc = [...$acc, $diff];
                return $acc; 
        }, []);
        return $diffColl;

}

                                 









