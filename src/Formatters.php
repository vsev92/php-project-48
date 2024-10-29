<?php

namespace Differ\Formatters;

use Differ\Differ;
use Differ\Differ\DiffStatus;
use Exception;

function getFormattedDiffCol($diffCol, $formatName)
{
    switch ($formatName) {
        case 'plain':
            $output = plainDumpDiffCol($diffCol);
            break;
        case 'stylish':
            $output = stylishDumpDiffCol($diffCol);
            break;
        case 'json':
            $output = jsonDumpDiffCol($diffCol);
            break;
        default:
            throw new Exception('Unsupported format');
    }
    return $output;
}



/////////functions for plain formatting

function plainDumpDiffCol($diffCol, $name = '')
{
    $keys = array_keys($diffCol);
    $formatted = array_reduce($keys, function ($acc, $key) use ($diffCol, $name) {
            $name = $name === '' ? $key : $name . "." . $key;
            $formattedDiff = plainDumpDiff($diffCol[$key], $name);
            $acc  = $acc . $formattedDiff;
            return $acc;
    }, '');
    return $formatted;
}

function plainDumpDiff($diff, $propertyName)
{
    $diffStatus = \Differ\Differ\getDiffStatus($diff);
    switch ($diffStatus) {
        case DiffStatus::noDifference:
            $output = '';
            break;
        case DiffStatus::added:
            $newValue = \Differ\Differ\getNewValue($diff);
            $newValue = getPlainValueEncode($newValue);
            $output = "Property '{$propertyName}' was added with value: {$newValue}\n";
            break;
        case DiffStatus::removed:
            $output = "Property '{$propertyName}' was removed\n";
            break;
        case DiffStatus::updated:
            $value  = \Differ\Differ\getValue($diff);
            $value  = getPlainValueEncode($value);
            $newValue = \Differ\Differ\getNewValue($diff);
            $newValue = getPlainValueEncode($newValue);
            $output = "Property '{$propertyName}' was updated. From {$value} to {$newValue}\n";
            break;
        case DiffStatus::parentDiffNode:
            $child = \Differ\Differ\getChild($diff);
            $output = plainDumpDiffCol($child, $propertyName);
            break;
        default:
            break;
    }
    return $output;
}

function getPlainValueEncode($value)
{
    $type = gettype($value);
    switch ($type) {
        case "boolean":
            return $value ? "true" : "false";
            break;
        case "NULL":
            return "null";
            break;
        case "array":
            return "[complex value]";
            break;
        case "string":
            return "'{$value}'";
            break;
        default:
            return  $value;
            break;
    }
}


/////////functions for stylish

function stylishDumpDiffCol($diffCol, $debt = 1)
{
    $keys = array_keys($diffCol);
    $formatted = array_reduce($keys, function ($acc, $key) use ($debt, $diffCol) {
            $margin = getMarginLeft($debt, STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_PROPERTIES);
            $formattedDiff = stylishDumpDiff($key, $diffCol[$key], $debt);
            $acc  = $acc . $formattedDiff;
            return $acc;
    }, '');

    $braceMargin = getMarginLeft(($debt - 1), STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_BRACES);
    $formatted =  "{\n" . $formatted . $braceMargin  . "}\n";
    return $formatted;
}

const STYLISH_SPACE_PER_LEVEL = 4;
const STYLISH_OFFSET_TO_LEFT_PROPERTIES = 2;
const STYLISH_OFFSET_TO_LEFT_BRACES = 0;

function getMarginLeft($debt, $spaceCountPerLevel, $offsetToLeft)
{
    $repeatCount = $debt * $spaceCountPerLevel - $offsetToLeft;
    $repeatCount = $repeatCount < 0 ? 0 : $repeatCount;
    return str_repeat(" ", $repeatCount);
}

const STYLISH_ADDDED = '+ ';
const STYLISH_REMOVED = '- ';
const STYLISH_NO_DIFFERENCE = '  ';


function stylishDumpDiff($key, $diff, $debt)
{
    $value = null;
    $newValue = null;
    if (\Differ\Differ\isKeyExistsInFirst($diff)) {
        $isValueComplex = \Differ\Differ\isFirstValueComplex($diff);
        $value = \Differ\Differ\getValue($diff);
        $value = formatValueToStylish($value, $isValueComplex, $debt);
        $spaceBeforeValue = $value === '' ? '' : ' ';
        $symbolAfterValue = $isValueComplex ? '' : "\n";
    }

    if (\Differ\Differ\isKeyExistsInSecond($diff)) {
        $isNewValueComplex = \Differ\Differ\isSecondValueComplex($diff);
        $newValue = \Differ\Differ\getnewValue($diff);
        $newValue = formatValueToStylish($newValue, $isNewValueComplex, $debt);
        $spaceBeforeNewValue = $newValue === '' ? '' : ' ';
        $symbolAfterNewValue = $isNewValueComplex ? '' : "\n";
    }

    $margin = getMarginLeft($debt, STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_PROPERTIES);
    $diffStatus = \Differ\Differ\getDiffStatus($diff);
    switch ($diffStatus) {
        case DiffStatus::noDifference:
            $output  = $margin . STYLISH_NO_DIFFERENCE . $key  . ": " . $value  . $symbolAfterValue;
            break;
        case DiffStatus::added:
            $output = $margin . STYLISH_ADDDED . $key  . ": " . $newValue  . $symbolAfterNewValue;
            break;
        case DiffStatus::removed:
            $output = $margin . STYLISH_REMOVED . $key  . ": " . $value  . $symbolAfterValue;
            break;
        case DiffStatus::updated:
            $result1 = $margin . STYLISH_REMOVED . $key  . ": " . $value  . $symbolAfterValue;
            $result2 = $margin . STYLISH_ADDDED . $key . ": " . $newValue  . $symbolAfterNewValue;
            $output = $result1 . $result2;
            break;
        case DiffStatus::parentDiffNode:
            $child = \Differ\Differ\getChild($diff);
            $output = $margin . STYLISH_NO_DIFFERENCE . $key . ": " . stylishDumpDiffCol($child, ($debt + 1));
            break;
        default:
            break;
    }
    return $output;
}

function formatValueToStylish($value, $isComplexValue, $debt)
{
    if ($isComplexValue) {
        return getStylishFromComplexValue($value, ($debt + 1));
    } else {
        return getStylishValueEncode($value);
    }
}

function getStylishFromComplexValue($complexValue, $debt)
{
        $keys = array_keys($complexValue);
        $braceMargin = getMarginLeft(($debt - 1), STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_BRACES);
        $elements = array_reduce($keys, function ($acc, $key) use ($complexValue, $debt) {
                $margin = getMarginLeft($debt, STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_PROPERTIES);
                $ValueIsArray = is_array($complexValue[$key]);
                $value = formatValueToStylish($complexValue[$key], $ValueIsArray, ($debt));
                $spaceBeforeValue = mb_strlen($value) > 0 ? ' ' : '';
                $symbolAfterValue = $ValueIsArray ? '' : "\n";
                $acc  = $acc . $margin . '  ' . $key . ":" . $spaceBeforeValue . $value . $symbolAfterValue;
                return $acc;
        }, '');
        $output =  "{\n" . $elements . $braceMargin  . "}\n";
        return $output;
}

function getStylishValueEncode($value)
{
    $type = gettype($value);
    switch ($type) {
        case "boolean":
            return $value ? "true" : "false";
        case "NULL":
            return "null";
        default:
            return  $value;
    }
}

function jsonDumpDiffCol($diffCol)
{
    return json_encode($diffCol, JSON_PRETTY_PRINT);
}
