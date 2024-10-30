<?php

namespace Differ\Formatters;

use Differ\Differ;
use Differ\Differ\DiffStatus;
use Exception;

function getFormattedDiffCol(array $diffCol, string $formatName)
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

function plainDumpDiffCol(array $diffCol, string $name = '')
{
    $keys = array_keys($diffCol);
    $formatted = array_reduce($keys, function ($acc, $key) use ($diffCol, $name) {
            $diffName = $name === '' ? $key : $name . "." . $key;
            $formattedDiff = plainDumpDiff($diffCol[$key], $diffName);
            $NewAcc  = $acc . $formattedDiff;
            return $NewAcc;
    }, '');
    return $formatted;
}

function plainDumpDiff(array $diff, string $propertyName)
{
    $diffStatus = \Differ\Differ\getDiffStatus($diff);
    switch ($diffStatus) {
        case DiffStatus::noDifference:
            return '';
        case DiffStatus::added:
            $newValue = \Differ\Differ\getNewValue($diff);
            $formattedNewValue = getPlainValueEncode($newValue);
            return "Property '{$propertyName}' was added with value: {$formattedNewValue}\n";
        case DiffStatus::removed:
            return "Property '{$propertyName}' was removed\n";
        case DiffStatus::updated:
            $value  = \Differ\Differ\getValue($diff);
            $formattedValue  = getPlainValueEncode($value);
            $newValue = \Differ\Differ\getNewValue($diff);
            $formattedNewValue = getPlainValueEncode($newValue);
            return "Property '{$propertyName}' was updated. From {$formattedValue} to {$formattedNewValue}\n";
        case DiffStatus::parentDiffNode:
            $child = \Differ\Differ\getChild($diff);
            return plainDumpDiffCol($child, $propertyName);
        default:
            return '';
    }
}

function getPlainValueEncode(mixed $value)
{
    $type = gettype($value);
    switch ($type) {
        case "boolean":
            return $value ? "true" : "false";
        case "NULL":
            return "null";
        case "array":
            return "[complex value]";
        case "string":
            return "'{$value}'";
        default:
            return  $value;
            break;
    }
}


/////////functions for stylish

function stylishDumpDiffCol(array $diffCol, int $debt = 1)
{
    $keys = array_keys($diffCol);
    $formatted = array_reduce($keys, function ($acc, $key) use ($debt, $diffCol) {
            $margin = getMarginLeft($debt, STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_PROPERTIES);
            $formattedDiff = stylishDumpDiff($key, $diffCol[$key], $debt);
            $acc  = $acc . $formattedDiff;
            return $acc;
    }, '');

    $braceMargin = getMarginLeft(($debt - 1), STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_BRACES);
    return "{\n" . $formatted . $braceMargin  . "}\n";
}

const STYLISH_SPACE_PER_LEVEL = 4;
const STYLISH_OFFSET_TO_LEFT_PROPERTIES = 2;
const STYLISH_OFFSET_TO_LEFT_BRACES = 0;

function getMarginLeft(int $debt, int $spaceCountPerLevel, int $offsetToLeft)
{
    $repeatCount = $debt * $spaceCountPerLevel - $offsetToLeft;
    //$repeatCount = $repeatCount < 0 ? 0 : $repeatCount;
    return str_repeat(" ", $repeatCount);
}

const STYLISH_ADDDED = '+ ';
const STYLISH_REMOVED = '- ';
const STYLISH_NO_DIFFERENCE = '  ';


function stylishDumpDiff(string $key, array $diff, int $debt)
{
    $margin = getMarginLeft($debt, STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_PROPERTIES);
    $diffStatus = \Differ\Differ\getDiffStatus($diff);
    switch ($diffStatus) {
        case DiffStatus::noDifference:
            return $margin . STYLISH_NO_DIFFERENCE . $key  . ": " . getFormattedValue($diff, $debt) . getSymbolAfterValue($diff);
        case DiffStatus::added:
            return $margin . STYLISH_ADDDED . $key  . ": " . getFormattedNewValue($diff, $debt)  . getSymbolAfterNewValue($diff);
        case DiffStatus::removed:
            return $margin . STYLISH_REMOVED . $key  . ": " . getFormattedValue($diff, $debt) . getSymbolAfterValue($diff);
        case DiffStatus::updated:
            $result1 = $margin . STYLISH_REMOVED . $key  . ": " . getFormattedValue($diff, $debt)  . getSymbolAfterValue($diff);
            $result2 = $margin . STYLISH_ADDDED . $key . ": " . getFormattedNewValue($diff, $debt)  . getSymbolAfterNewValue($diff);
            return $result1 . $result2;
        case DiffStatus::parentDiffNode:
            $child = \Differ\Differ\getChild($diff);
            return $margin . STYLISH_NO_DIFFERENCE . $key . ": " . stylishDumpDiffCol($child, ($debt + 1));
        default:
            return '';
    }
}

function getSymbolAfterValue(array $diff)
{
    $result = \Differ\Differ\isFirstValueComplex($diff) ? '' : "\n";
    return $result;
}

function getSymbolAfterNewValue(array $diff)
{
    $result = \Differ\Differ\isSecondValueComplex($diff) ? '' : "\n";
    return $result;
}

function getFormattedValue(array $diff, int $debt)
{
    $value = \Differ\Differ\getValue($diff);
    $isValueComplex = \Differ\Differ\isFirstValueComplex($diff);
    return formatToStylish($value, $isValueComplex, $debt);
}

function getFormattedNewValue(array $diff, int $debt)
{
    $NewValue = \Differ\Differ\getNewValue($diff);
    $isNewValueComplex = \Differ\Differ\isSecondValueComplex($diff);
    return formatToStylish($NewValue, $isNewValueComplex, $debt);
}

function formatToStylish(mixed $value, bool $isComplexValue, int $debt)
{
    if ($isComplexValue) {
        return getStylishFromComplexValue($value, ($debt + 1));
    } else {
        return getStylishValueEncode($value);
    }
}



function getStylishFromComplexValue(array $complexValue, int $debt)
{
        $keys = array_keys($complexValue);
        $braceMargin = getMarginLeft(($debt - 1), STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_BRACES);
        $elements = array_reduce($keys, function ($acc, $key) use ($complexValue, $debt) {
                $margin = getMarginLeft($debt, STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_PROPERTIES);
                $ValueIsArray = is_array($complexValue[$key]);
                $value = formatToStylish($complexValue[$key], $ValueIsArray, ($debt));
                $spaceBeforeValue = mb_strlen($value) > 0 ? ' ' : '';
                $symbolAfterValue = $ValueIsArray ? '' : "\n";
                $newAcc = $acc . $margin . '  ' . $key . ":" . $spaceBeforeValue . $value . $symbolAfterValue;
                return $newAcc;
        }, '');
        $output =  "{\n" . $elements . $braceMargin  . "}\n";
        return $output;
}

function getStylishValueEncode(mixed $value)
{
    $type = gettype($value);
    switch ($type) {
        case "boolean":
            return (bool)$value ? "true" : "false";
        case "NULL":
            return "null";
        default:
            return  $value;
    }
}

function jsonDumpDiffCol(array $diffCol)
{
    return json_encode($diffCol, JSON_PRETTY_PRINT);
}
