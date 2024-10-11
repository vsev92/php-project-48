<?php

namespace Gendiff\Diff;


function getJsonItemsCollection($assocArrayJson) {
        $keys = array_keys($assocArrayJson);
        return array_map(fn($key)=> ['jsonKey' => $key, 'jsonValue' => $assocArrayJson[$key]], $keys);
}

function getJsonItemWithKey($jsonItemsCollection, $key) {

        return ...array_filter($jsonItemsCollection, fn($item)=> $item['jsonKey'] === $key);
}

function getJsonKey($jsonItem) {
        return $jsonItem['jsonKey'];
}


function getJsonValue($jsonItem) {
        return $jsonItem['jsonValue'];
}

function getUniqueKeys($jsonItemsCollection1, $jsonItemsCollection1) {
        $keys1 = array_map(fn($item) => getJsonKey($item), $jsonItemsCollection1);
        $keys2 = array_map(fn($item) => getJsonKey($item), $jsonItemsCollection2);
        $commonArray = array_merge($keys1, $keys2);
        $keys = array_keys($commonArray);
        sort($keys, SORT_STRING);
        return $keys;    
}

function getDiffByKey($jsonKey, $jsonItemsCollection1,$jsonItemsCollection2) {
        $jsonItem1 = getJsonItemWithKey($jsonItemsCollection1, $jsonKey); 
        $jsonItem2 = getJsonItemWithKey($jsonItemsCollection1, $jsonKey); 
        $diff = [];

        if (!is_null($jsonItem1) && !is_null($jsonItem2)) {
                if (getJsonValue($jsonItem1) === getJsonValue($jsonItem2)) {
                        $diff[] = getJsonKey($jsonItem1) . ":" . getJsonValue($jsonItem1);    
                } else {
                        $diff[] = "-" . " " .  getJsonKey($jsonItem1) . ":" . getJsonValue($jsonItem1);
                        $diff[] = "+" . " " .  getJsonKey($jsonItem2) . ":" . getJsonValue($jsonItem2);

                }
        } else {
                if (is_null($jsonItem1)) {
                        $diff[] = "+" . " " .  getJsonKey($jsonItem2) . ":" . getJsonValue($jsonItem2);
                }
                if (is_null($jsonItem2)) {
                        $diff[] = "-" . " " .  getJsonKey($jsonItem1) . ":" . getJsonValue($jsonItem1);
                }


        }
        return $diff;
  
}


function getDiffByCollections($jsonItemsCollection1,$jsonItemsCollection2) {
        $keys = Gendiff\Diff\getUniqueKeys($jsonItemsCollection1, $jsonItemsCollection2);
        return array_reduce($keys, function($acc, $key) use ($jsonItemsCollection1, $jsonItemsCollection2) {
                $diff = getDiffByKey($key, $jsonItemsCollection1,$jsonItemsCollection2); 
                $acc = [...$acc, ...$diff];
                return $acc; 
        }, []);
  
}
