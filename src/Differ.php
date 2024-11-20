<?php

namespace Differ\Differ;

use  Exception;
use  Differ\DifferStatus\DiffStatus;

use function Functional\sort;
use function Differ\Parsers\parse;
use function Differ\Formatters\getFormattedDiffCol;

enum SourceType
{
    case json;
    case yaml;
}

function getSourceType(string $filePath)
{
    $pathInfo = pathinfo($filePath);
    $extension = $pathInfo['extension'];
    switch ($extension) {
        case 'json':
            return SourceType::json;
        case 'yaml':
            return SourceType::yaml;
        case 'yml':
            return SourceType::yaml;
        default:
            throw new InvalidArgumentException('wrong file extension' . $filePath);
    }
}

function readSourceData($filePath)
{
    if (file_exists($filePath)) {
        return file_get_contents($filePath);
    } else {
        throw new InvalidArgumentException('wrong filename ' . $filePath);
    }
}

function genDiff(string $pathToFile1, string $pathToFile2, string $formatName = 'stylish')
{
        $sourceType1 = getSourceType($pathToFile1);
        $sourceType2 = getSourceType($pathToFile2);
        $sourceData1 = readSourceData($pathToFile1);
        $sourceData2 = readSourceData($pathToFile2);
        $collection1 = parse($sourceData1, $sourceType1);
        $collection2  = parse($sourceData2, $sourceType2);
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
