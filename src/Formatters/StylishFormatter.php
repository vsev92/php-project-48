<?php

namespace Differ\Formatters\StylishFormatter;

use Differ\Differ\DiffStatus;
use Exception;

use function Differ\Differ\getDiffStatus;
use function Differ\Differ\getValue;
use function Differ\Differ\getNewValue;
use function Differ\Differ\getChild;
use function Differ\Differ\isFirstValueComplex;
use function Differ\Differ\isSecondValueComplex;

function stylishDumpDiffCol(array $diffCol, int $debt = 1)
{
    $keys = array_keys($diffCol);
    $formatted = array_reduce($keys, function ($acc, $key) use ($debt, $diffCol) {
            $margin = getMarginLeft($debt, STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_PROPERTIES);
            $formattedDiff = stylishDumpDiff($key, $diffCol[$key], $debt);
            $newAcc  = $acc . $formattedDiff;
            return $newAcc;
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
    return str_repeat(" ", $repeatCount);
}

const STYLISH_ADDDED = '+ ';
const STYLISH_REMOVED = '- ';
const STYLISH_NO_DIFFERENCE = '  ';


function stylishDumpDiff(string $key, array $diff, int $debt)
{
    $margin = getMarginLeft($debt, STYLISH_SPACE_PER_LEVEL, STYLISH_OFFSET_TO_LEFT_PROPERTIES);
    $diffStatus = getDiffStatus($diff);
    $stylishKeyToAdded = $margin . STYLISH_ADDDED . $key  . ": ";
    $stylishKeyToRemoved = $margin . STYLISH_REMOVED . $key  . ": ";
    $stylishKeyNoDifference = $margin . STYLISH_NO_DIFFERENCE . $key  . ": ";

    switch ($diffStatus) {
        case DiffStatus::noDifference:
            $stylishValue = getFormattedValue($diff, $debt) . getSymbolAfterValue($diff);
            return $stylishKeyNoDifference . $stylishValue;
        case DiffStatus::added:
            $stylishValue = getFormattedNewValue($diff, $debt)  . getSymbolAfterNewValue($diff);
            return $stylishKeyToAdded . $stylishValue;
        case DiffStatus::removed:
            $stylishValue = getFormattedValue($diff, $debt) . getSymbolAfterValue($diff);
            return $stylishKeyToRemoved . $stylishValue;
        case DiffStatus::updated:
            $stylishValue = getFormattedValue($diff, $debt)  . getSymbolAfterValue($diff);
            $stylishNewValue = getFormattedNewValue($diff, $debt)  . getSymbolAfterNewValue($diff);
            return $stylishKeyToRemoved . $stylishValue . $stylishKeyToAdded . $stylishNewValue;
        case DiffStatus::parentDiffNode:
            $child = getChild($diff);
            return $stylishKeyNoDifference . stylishDumpDiffCol($child, ($debt + 1));
        default:
            return '';
    }
}

function getSymbolAfterValue(array $diff)
{
    $result = isFirstValueComplex($diff) ? '' : "\n";
    return $result;
}

function getSymbolAfterNewValue(array $diff)
{
    $result = isSecondValueComplex($diff) ? '' : "\n";
    return $result;
}

function getFormattedValue(array $diff, int $debt)
{
    $value = getValue($diff);
    $isValueComplex = isFirstValueComplex($diff);
    return formatToStylish($value, $isValueComplex, $debt);
}

function getFormattedNewValue(array $diff, int $debt)
{
    $NewValue = getNewValue($diff);
    $isNewValueComplex = isSecondValueComplex($diff);
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
