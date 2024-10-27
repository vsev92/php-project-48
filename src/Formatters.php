<?php

namespace Gendiff\Formatters;

use Gendiff\Diff;
use Gendiff\Diff\DiffStatus;
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
                $output = jsonDumpDumpDiffCol($diffCol);
            break;
        default:
            throw new Exception('Unsupported format');
        }
    return $output;
}



/////////functions for plain formatting

function plainDumpDiffCol($diffCol, $name = '') {
    $formatted = array_reduce($diffCol, function($acc, $diff) use ($name) {
            $key = \Gendiff\Diff\getKey($diff);
            $name = $name === '' ? $key : $name . "." . $key;
            $formattedDiff = plainDumpDiff($diff, $name);
            $acc  = $acc . $formattedDiff;
            return $acc;
    },'');
return $formatted;

}

function plainDumpDiff($diff, $propertyName)
{
    $diffStatus = \Gendiff\Diff\getDiffStatus($diff);
    switch ($diffStatus) {
            case DiffStatus::noDifference:
                    $output = '';
                break;
            case DiffStatus::added:
                    $newValue = \Gendiff\Diff\getNewValue($diff);
                    $newValue = getPlainValueEncode($newValue);
                    $output = "Property '{$propertyName}' was added with value: {$newValue}\n";
                break;
            case DiffStatus::removed:
                    $output = "Property '{$propertyName}' was removed\n";
                break;
            case DiffStatus::updated:
                    $value  = \Gendiff\Diff\getValue($diff);
                    $value  = getPlainValueEncode($value);
                    $newValue = \Gendiff\Diff\getNewValue($diff);
                    $newValue = getPlainValueEncode($newValue);
                    $output = "Property '{$propertyName}' was updated. From {$value} to {$newValue}\n";
                break;
            case DiffStatus::parentDiffNode:
                    $child = \Gendiff\Diff\getChild($diff);
                    $output = plainDumpDiffCol($child, $propertyName);
                break;
            default:
                break;
    }
    return $output;


}

function getPlainValueEncode($value) {
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

function stylishDumpDiffCol($diffCol, $debt = 1) {
    $formatted = array_reduce($diffCol, function($acc, $diff) use ($debt) {
            $key = \Gendiff\Diff\getKey($diff);
            $margin = getMarginLeft($debt, 4, 2); 
            $formattedDiff = stylishDumpDiff($diff, $debt);
            $acc  = $acc . $formattedDiff;
            return $acc;
    },'');

    $braceMargin = getMarginLeft(($debt - 1),  4, 0);
    $formatted =  "{\n" . $formatted . $braceMargin  ."}\n";
return $formatted;
}


const STYLISH_SYMBOL_ADDDED = '+ ';
const STYLISH_SYMBOL_REMOVED = '- ';
const STYLISH_SYMBOL_NO_DIFFERENCE = '  ';


function stylishDumpDiff($diff, $debt)
{
    $value = null;
    $newValue = null;
    if (\Gendiff\Diff\isKeyExistsInFirst($diff)) {
        $isValueComplex = \Gendiff\Diff\isFirstValueComplex($diff);
        $value = \Gendiff\Diff\getValue($diff);
        $value = formatValueToStylish($value, $isValueComplex, $debt);
        $spaceBeforeValue = $value === '' ? '' : ' ';
        $symbolAfterValue = $isValueComplex ? '' : "\n";
    }

    if (\Gendiff\Diff\isKeyExistsInSecond($diff)) {
        $isNewValueComplex = \Gendiff\Diff\isSecondValueComplex($diff);
        $newValue = \Gendiff\Diff\getnewValue($diff);
        $newValue = formatValueToStylish($newValue, $isNewValueComplex, $debt);
        $spaceBeforeNewValue = $newValue === '' ? '' : ' ';
        $symbolAfterNewValue = $isNewValueComplex ? '' : "\n";
    }
    $key = \Gendiff\Diff\getKey($diff);
    $margin = getMarginLeft($debt, 4, 2);

    $diffStatus = \Gendiff\Diff\getDiffStatus($diff);
    switch ($diffStatus) {
            case DiffStatus::noDifference:
                $output  = $margin . STYLISH_SYMBOL_NO_DIFFERENCE . $key  . ":" . $spaceBeforeValue . $value  . $symbolAfterValue;
                break;
            case DiffStatus::added:
                $output  = $margin . STYLISH_SYMBOL_ADDDED  . $key  . ":" . $spaceBeforeNewValue . $newValue  . $symbolAfterNewValue;
                break;
            case DiffStatus::removed:
                $output  = $margin . STYLISH_SYMBOL_REMOVED  . $key  . ":" . $spaceBeforeValue . $value  . $symbolAfterValue;
                break;
            case DiffStatus::updated:
                $output  = $margin . STYLISH_SYMBOL_REMOVED  . $key  . ":" . $spaceBeforeValue . $value  . $symbolAfterValue;
                $output = $output . $margin . STYLISH_SYMBOL_ADDDED  . $key  . ":" . $spaceBeforeNewValue . $newValue  . $symbolAfterNewValue;
                break;
            case DiffStatus::parentDiffNode:
                $child = \Gendiff\Diff\getChild($diff);
                $output = $margin . STYLISH_SYMBOL_NO_DIFFERENCE . $key . ": " . stylishDumpDiffCol($child, ($debt + 1));
                break;
            default:
                break;
    }
    return $output;
}

function formatValueToStylish($value, $isComplexValue, $debt)
{      
    $value = $isComplexValue ? getStylishFromComplexValue($value, ($debt + 1), $formatName) : getStylishValueEncode($value);    
    return $value;
}

function getStylishFromComplexValue($complexValue, $debt)
{
        $keys = array_keys($complexValue);
        $braceMargin = getMarginLeft(($debt - 1),  4, 0);
        $elements = array_reduce($keys, function($acc, $key) use ($complexValue, $debt, $formatName) {
                $margin = getMarginLeft($debt, 4, 2);
                $ValueIsArray = is_array($complexValue[$key]);
                $value = formatValueToStylish($complexValue[$key], $ValueIsArray, ($debt));
                $spaceBeforeValue = mb_strlen($value) > 0 ? ' ' : '';
                $symbolAfterValue = $ValueIsArray ? '' : "\n";
                $acc  = $acc . $margin . '  ' . $key . ":" . $spaceBeforeValue . $value . $symbolAfterValue;
                return $acc;
        },'');
        $output =  "{\n" . $elements . $braceMargin  ."}\n";
        return $output;
}

function getStylishValueEncode($value) {
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


function getMarginLeft($debt, $spaceCountPerLevel, $offsetToLeft) {
        $repeatCount = $debt * $spaceCountPerLevel - $offsetToLeft;
        $repeatCount = $repeatCount < 0 ? 0 : $repeatCount;
        return str_repeat(" ", $repeatCount);
}

/////////functions for  json only



function jsonDumpDiffCol($diffCol, $debt=1) 
{
    $keys = array_keys($diffCol);
    $lastKey = $keys[count($keys) - 1];
    $output = array_reduce($keys, function($acc, $key) use ($diffCol, $debt, $lastKey) {
        $comma = $key === $lastKey ? '' : ',';
        $acc = $acc . jsonDumpDiff($diffCol[$key], $debt, $comma);
        return $acc;
    },'');
    $output = $debt === 1 ? "{\n" . $output ."}\n" : $output;
    return $output;

}

function jsonDumpDiff($diff, $debt, $comma) 
{
    $key = \Gendiff\Diff\getKey($diff);
    $jsonKey = getFormattedJsonKey($diff);
    $margin = getMarginLeft($debt, 4, 0);
    if (\Gendiff\Diff\getDiffStatus($diff) !== DiffStatus::parentDiffNode) {
        $formattedProperies = jsonDumpProperties($diff, ($debt + 1));
        $json = $formattedProperies;


    } else {
        $json = jsonDumpDiffCol(\Gendiff\Diff\getChild($diff), ($debt + 1)); 
    }
    return  $margin . $jsonKey . ": {\n" . $json . $margin ."}" . $comma . "\n";

}

function jsonDumpProperties($diff, $debt) 
{
    $properties = getDiffProperties($diff);
    $keys = array_keys($properties);
    $lastKey = $keys[count($keys) - 1];

    $output = array_reduce($keys, function($acc, $key) use ($properties, $debt, $lastKey) {
        $margin = getMarginLeft($debt, 4, 0);
        $comma = $key === $lastKey ? '' : ',';
        $jsonKey = formatValueToJson($key, false, $debt);
        $jsonValue = json_encode($properties[$key]);
        $acc = $acc . $margin . $jsonKey . ": " . $jsonValue . $comma . "\n";
        return $acc;
    },'');
    return $output;
}

function getDiffProperties($diff) 
{
    return array_filter($diff, fn($key)=> $key!=='key',ARRAY_FILTER_USE_KEY);
}

function formatJsonKey($key, $formatName)
{
    $key = "\"{$key}\"";
    return $key;
}

function getFormattedJsonKey($diff)
{
        $key = \Gendiff\Diff\getKey($diff);
        return formatJsonKey($key, $formatName);
}


function formatValueToJson($value, $isComplexValue, $debt, $comma = '')
{      
    $value = $isComplexValue ? getNestedFormatFromComplexValue($value, ($debt), $formatName) : json_encode($value);
    return $value;
}

function getJsonFormatFromComplexValue($complexValue, $debt, $comma = '')
{
        
        $braceMargin = getMarginLeft(($debt - 1),  4, 0);
        $keys = array_keys($complexValue);
        $lastKey = $keys[count($keys) - 1];
        $elements = array_reduce($keys, function($acc, $key) use ($complexValue, $debt, $formatName, $lastKey) {
                $margin = getMarginLeft($debt, 4, 2);
                $ValueIsArray = is_array($complexValue[$key]);
                $comma = $key !== $lastKey  ? "," : "";
                $value = formatValueToJson($complexValue[$key], $ValueIsArray, ($debt + 1), $formatName);
                $spaceBeforeValue = mb_strlen($value) > 0 ? ' ' : '';
                $symbolAfterValue = $ValueIsArray ? '' : "\n";
                $key = formatJsonKey($key, $formatName);
                $acc  = $acc . $margin . '  ' . $key . ":" . $spaceBeforeValue . $value . $comma . $symbolAfterValue;
                return $acc;
        },'');
        $output =  "{\n" . $elements . $braceMargin  ."}\n";
        return $output;
}