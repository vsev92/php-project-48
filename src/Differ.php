<?php

namespace Differ\Differ;

use  Exception;
use  Differ\DifferStatus\DiffStatus;

use function Functional\sort;
use function Differ\Parsers\getSourceType;
use function Differ\Parsers\parseFromFile;
use function Differ\Formatters\getFormattedDiffCol;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = 'stylish')
{
        $sourceType1 = getSourceType($pathToFile1);
        $sourceType2 = getSourceType($pathToFile2);
        $collection1 = parseFromFile($pathToFile1, $sourceType1);
        $collection2  = parseFromFile($pathToFile2, $sourceType2);
        $diffCol = makeDiffCollection($collection1, $collection2);
        return getFormattedDiffCol($diffCol, $formatName);
}

function makeDiffCollection(array $collection1, array $collection2)
{
        $keys =  getUniqueKeys($collection1, $collection2);
        $diffColl =  array_reduce($keys, function ($acc, $key) use ($collection1, $collection2) {
                if (!array_key_exists($key, $collection1)) {
                        return [...$acc, $key => ['diffStatus' => DiffStatus::added, 'newValue' => $collection2[$key]]];
                }

                if (!array_key_exists($key, $collection2)) {
                        return [...$acc, $key => ['diffStatus' => DiffStatus::removed, 'value' => $collection1[$key]]];
                }

                $value1 = $collection1[$key];
                $value2 = $collection2[$key];

                if (is_array($value1) && is_array($value2)) {
                        $childDiffColl = makeDiffCollection($collection1[$key], $collection2[$key]);
                        return [...$acc, $key => ['diffStatus' => DiffStatus::parentDiffNode, 'Child' => $childDiffColl]];
                }

                if ($value1 === $value2) {
                        return [...$acc, $key => ['diffStatus' => DiffStatus::noDifference, 'value' => $value1]];
                }
                
                return [...$acc, $key => ['diffStatus' => DiffStatus::updated, 
                                          'value' => $value1,
                                          'newValue' => $value2
                                          ]];
                
        }, []);
        return $diffColl;

}

function getUniqueKeys(array $Collection1, array $Collection2)
{
        $keys1 = array_keys($Collection1);
        $keys2 = array_keys($Collection2);
        $commonKeysCollection = array_merge($keys1, $keys2);
        $uniqueKeysCollection = array_unique($commonKeysCollection, SORT_STRING);
        $sortedKeysCollection = sort($uniqueKeysCollection, fn ($left, $right) => strcmp($left, $right));
        return $sortedKeysCollection;
}


function getDiffStatus(array $diff)
{
       return $diff['diffStatus'];
}

function hasChild(array $diff)
{
       return $diff['diffStatus'] === DiffStatus::parentDiffNode;
}
function getChild(array $diff)
{
       $child =  hasChild($diff) ? $diff['Child'] : throw new Exception("no child in diff");
       return $child;
}


function isKeyExistsInFirst(array $diff)
{
       return  array_key_exists('value', $diff);
}

function getValue(array $diff)
{
    if (isKeyExistsInFirst($diff)) {
        return $diff['value'];
    } else {
        throw new Exception("Value  not found in first collection");
    }
}

function isKeyExistsInSecond(array $diff)
{
        return  array_key_exists('newValue', $diff);
}

function getNewValue(array $diff)
{
    if (isKeyExistsInSecond($diff)) {
        return $diff['newValue'];
    } else {
        throw new Exception("Value  not found in second collection");
    }
}

function isFirstValueComplex(array $diff)
{
       return  is_array(getValue($diff));
}

function isSecondValueComplex(array $diff)
{
        return  is_array(getNewValue($diff));
}
