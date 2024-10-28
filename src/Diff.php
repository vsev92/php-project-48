<?php

namespace Gendiff\Diff;

use  Gendiff\Formatters;
use  Gendiff\Parser;
use  Gendiff\Parser\SourceType;
use  Symfony\Component\Yaml\Yaml;
use  Exception;

/////////// functions for make diff objects
function genDiff($pathToFile1, $pathToFile2, $formatName)
{
        $sourceType1 = \Gendiff\Parser\getSourceType($pathToFile1);
        $sourceType2 = \Gendiff\Parser\getSourceType($pathToFile2);
        $collection1 = \Gendiff\Parser\parseFromFile($pathToFile1, $sourceType1);
        $collection2  = \Gendiff\Parser\parseFromFile($pathToFile2, $sourceType2);
        $diffCol = makeDiffCollection($collection1, $collection2);
        return \Gendiff\Formatters\getFormattedDiffCol($diffCol, $formatName);
}


function makeDiffCollection($collection1, $collection2)
{
        $keys =  getUniqueKeys($collection1, $collection2);
        $diffColl =  array_reduce($keys, function ($acc, $key) use ($collection1, $collection2) {
                $diff = makeDiff($key, $collection1, $collection2);
                $acc[$key] = $diff;
                return $acc;
        }, []);
        return $diffColl;
}

function makeDiff($key, $collection1, $collection2)
{
    $diff = [];
    $ExistInCollection1 = array_key_exists($key, $collection1);
    $ExistInCollection2 = array_key_exists($key, $collection2);
    $ValueIsArray1 = $ExistInCollection1 && is_array($collection1[$key]);
    $ValueIsArray2 = $ExistInCollection2 && is_array($collection2[$key]);
    if ($ExistInCollection1 && $ExistInCollection2) {
        $diff = getDiffForBothExistsValues($collection1[$key], $collection2[$key]);
    } else {
        if (!$ExistInCollection1) {
                $diff = getDiffForOnlySecondExistValue($collection2[$key]);
        }
        if (!$ExistInCollection2) {
                $diff = getDiffForOnlyFirstExistValue($collection1[$key]);
        }
    }
    return $diff;
}

enum DiffStatus: string
{
    case added = 'added';
    case removed = 'removed';
    case updated = 'updated';
    case noDifference = 'noDifference';
    case parentDiffNode = 'parentDiffNode';
}

function getDiffForBothExistsValues($value1, $value2)
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



function getDiffForOnlyFirstExistValue($leftValue)
{
        $ValueIsArray = is_array($leftValue);
        $diff  = [
                   'diffStatus' => DiffStatus::removed,
                   'value' => $leftValue,
                ];
        return $diff;
}

function getDiffForOnlySecondExistValue($rightValue)
{
        $valueIsArray = is_array($rightValue);
        $diff = [
                'diffStatus' => DiffStatus::added,
                'newValue' => $rightValue,
        ];
        return $diff;
}


function getUniqueKeys($Collection1, $Collection2)
{
        $keys1 = array_keys($Collection1);
        $keys2 = array_keys($Collection2);
        $commonKeysCollection = array_merge($keys1, $keys2);
        $commonKeysCollection = array_unique($commonKeysCollection, SORT_STRING);
        sort($commonKeysCollection, SORT_STRING);
        return $commonKeysCollection;
}


function getDiffStatus($diff)
{
       return $diff['diffStatus'];
}

function hasChild($diff)
{
       return $diff['diffStatus'] === diffStatus::parentDiffNode;
}
function getChild($diff)
{
       $child =  hasChild($diff) ? $diff['Child'] : throw new Exception("no child in diff");
       return $child;
}


function isKeyExistsInFirst($diff)
{
       return  array_key_exists('value', $diff);
}

function getValue($diff)
{
    if (isKeyExistsInFirst($diff)) {
        return $diff['value'];
    } else {
        throw new Exception("Value  not found in first collection");
    }
}

function isKeyExistsInSecond($diff)
{
        return  array_key_exists('newValue', $diff);
}

function getNewValue($diff)
{
    if (isKeyExistsInSecond($diff)) {
        return $diff['newValue'];
    } else {
        throw new Exception("Value  not found in second collection");
    }
}

function isFirstValueComplex($diff)
{
       return  is_array(getValue($diff));
}

function isSecondValueComplex($diff)
{
        return  is_array(getNewValue($diff));
}
