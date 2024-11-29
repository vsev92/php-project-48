<?php

namespace Differ\Differ;

use  InvalidArgumentException;
use  Differ\DifferStatus\DiffStatus;
use  Differ\FileFormat\FileFormat;

use function Functional\sort;
use function Differ\Parsers\parse;
use function Differ\Formatters\getFormattedDiffCol;

function getDataFormat(string $filePath)
{
    $pathInfo = pathinfo($filePath);
    if (isset($pathInfo['extension'])) {
        $extension = $pathInfo['extension'];
        switch ($extension) {
            case 'json':
                return FileFormat::json;
            case 'yaml' || 'yml':
                return FileFormat::yaml;
            default:
                throw new InvalidArgumentException('wrong file extension' . $filePath);
        }
    } else {
        throw new InvalidArgumentException('input file without extension' . $filePath);
    }
}

function getData(string $filePath)
{
    if (file_exists($filePath)) {
        return file_get_contents($filePath);
    } else {
        throw new InvalidArgumentException('wrong filename ' . $filePath);
    }
}

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = 'stylish')
{
        $FileFormat1 = getDataFormat($pathToFile1);
        $FileFormat2 = getDataFormat($pathToFile2);
        $sourceData1 = getData($pathToFile1);
        $sourceData2 = getData($pathToFile2);
        $collection1 = parse($sourceData1, $FileFormat1);
        $collection2  = parse($sourceData2, $FileFormat2);
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
                return [...$acc, $key => ['diffStatus' => DiffStatus::parentDiffNode, 'child' => $childDiffColl]];
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
