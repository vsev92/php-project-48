<?php

namespace Differ\Formatters\StylishFormatter;

use  Differ\DifferStatus\DiffStatus;
use Exception;

const COUNT_SPACE_PER_LEVEL = 4;
const OFFSET_TO_LEFT_PROPERTIES = 2;
const OFFSET_TO_LEFT_BRACES = 0;
const SYMBOL_ADD = '+ ';
const SYMBOL_REMOVE = '- ';
const SYMBOL_NO_DIFFERENCE = '  ';

function stylishDumpDiffCol(array $diffCol, int $debt = 1)
{
    $keys = array_keys($diffCol);
    $formatted = array_reduce($keys, function ($acc, $key) use ($debt, $diffCol) {
            $formattedDiff = stylishDumpDiff($key, $diffCol[$key], $debt);
            $newAcc  = $acc . $formattedDiff;
            return $newAcc;
    }, '');

    $braceOffset = getOffsetLeft(($debt - 1), COUNT_SPACE_PER_LEVEL, OFFSET_TO_LEFT_BRACES);
    return "{\n" . $formatted . $braceOffset  . "}\n";
}


function getOffsetLeft(int $debt, int $spaceCountPerLevel, int $offsetToLeft)
{
    $repeatCount = $debt * $spaceCountPerLevel - $offsetToLeft;
    return str_repeat(' ', $repeatCount);
}

function stylishDumpDiff(string $key, array $diff, int $debt)
{
    $margin = getOffsetLeft($debt, COUNT_SPACE_PER_LEVEL, OFFSET_TO_LEFT_PROPERTIES);
    $diffStatus = $diff['diffStatus'];
    $stylishKeyToAdded = $margin . SYMBOL_ADD . $key  . ': ';
    $stylishKeyToRemoved = $margin . SYMBOL_REMOVE . $key  . ': ';
    $stylishKeyNoDifference = $margin . SYMBOL_NO_DIFFERENCE . $key  . ': ';

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
            $child = $diff['child'];
            return $stylishKeyNoDifference . stylishDumpDiffCol($child, ($debt + 1));
        default:
            return '';
    }
}

function getSymbolAfterValue(array $diff)
{
    return is_array($diff['value']) ? '' : "\n";
}

function getSymbolAfterNewValue(array $diff)
{
    return is_array($diff['newValue']) ? '' : "\n";
}

function getFormattedValue(array $diff, int $debt)
{
    $value = $diff['value'];
    $isValueComplex = is_array($value);
    return formatToStylish($value, $isValueComplex, $debt);
}

function getFormattedNewValue(array $diff, int $debt)
{
    $NewValue = $diff['newValue'];
    $isNewValueComplex = is_array($NewValue);
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
        $braceOffset = getOffsetLeft(($debt - 1), COUNT_SPACE_PER_LEVEL, OFFSET_TO_LEFT_BRACES);
        $elements = array_reduce($keys, function ($acc, $key) use ($complexValue, $debt) {
                $margin = getOffsetLeft($debt, COUNT_SPACE_PER_LEVEL, OFFSET_TO_LEFT_PROPERTIES);
                $ValueIsArray = is_array($complexValue[$key]);
                $value = formatToStylish($complexValue[$key], $ValueIsArray, ($debt));
                $spaceBeforeValue = mb_strlen($value) > 0 ? ' ' : '';
                $symbolAfterValue = $ValueIsArray ? '' : "\n";
                $newAcc = $acc . $margin . '  ' . $key . ':' . $spaceBeforeValue . $value . $symbolAfterValue;
                return $newAcc;
        }, '');
        $output =  "{\n" . $elements . $braceOffset  . "}\n";
        return $output;
}

function getStylishValueEncode(mixed $value)
{
    $type = gettype($value);
    switch ($type) {
        case 'boolean':
            return (bool)$value ? 'true' : 'false';
        case 'NULL':
            return 'null';
        default:
            return  $value;
    }
}
