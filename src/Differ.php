<?php

namespace Differ\Differ;

use  Differ\Parser\SourceType;
use  Symfony\Component\Yaml\Yaml;
use  Exception;

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
                $diff = makeDiff($key, $collection1, $collection2);
                $newAcc = [...$acc, $key => $diff];
                return $newAcc;
        }, []);
        return $diffColl;
}

function makeDiff(string $key, array $collection1, array $collection2)
{
    $ExistInCollection1 = array_key_exists($key, $collection1);
    $ExistInCollection2 = array_key_exists($key, $collection2);
    $ValueIsArray1 = $ExistInCollection1 && is_array($collection1[$key]);
    $ValueIsArray2 = $ExistInCollection2 && is_array($collection2[$key]);
    if ($ExistInCollection1 && $ExistInCollection2) {
        return getDiffForBothExistsValues($collection1[$key], $collection2[$key]);
    } else {
        if (!$ExistInCollection1) {
                return getDiffForOnlySecondExistValue($collection2[$key]);
        }
        if (!$ExistInCollection2) {
                return getDiffForOnlyFirstExistValue($collection1[$key]);
        }
    }
}

enum DiffStatus: string
{
    case added = 'added';
    case removed = 'removed';
    case updated = 'updated';
    case noDifference = 'noDifference';
    case parentDiffNode = 'parentDiffNode';
}

function getDiffForBothExistsValues(mixed $value1, mixed $value2)
{
        $ValueIsArray1 = is_array($value1);
        $ValueIsArray2 = is_array($value2);
    if ($ValueIsArray1 && $ValueIsArray2) {
                $diffByColl = makeDiffCollection($value1, $value2);
                $diff  = [
                            'diffStatus' => DiffStatus::parentDiffNode,
                            'Child' => $diffByColl,
                        ];
    } elseif (!$ValueIsArray1 && !$ValueIsArray2) {
        if ($value1 === $value2) {
                $diff  = [
                        'diffStatus' => DiffStatus::noDifference,
                        'value' => $value1,
                ];
        } else {
                $diff  = [
                        'diffStatus' => DiffStatus::updated,
                        'value' => $value1,
                        'newValue' => $value2,
                ];
        }
    } else {
                $diff  = [
                          'diffStatus' => DiffStatus::updated,
                          'value' => $value1,
                          'newValue' => $value2,
                ];
    }
        return $diff;
}



function getDiffForOnlyFirstExistValue(mixed $leftValue)
{
        $ValueIsArray = is_array($leftValue);
        $diff  = [
                   'diffStatus' => DiffStatus::removed,
                   'value' => $leftValue,
                ];
        return $diff;
}

function getDiffForOnlySecondExistValue(mixed $rightValue)
{
        $valueIsArray = is_array($rightValue);
        $diff = [
                'diffStatus' => DiffStatus::added,
                'newValue' => $rightValue,
        ];
        return $diff;
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
