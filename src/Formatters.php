<?php

namespace Gendiff\Formatters;

use Gendiff\Diff;
use Gendiff\Diff\DiffStatus;
use Exception;

function getFormattedDiffCol($diffCol, $formatName, $debt = 1, $name = '') {
        $formatted = array_reduce($diffCol, function($acc, $diff) use ($name, $formatName, $debt) {
                $key = \Gendiff\Diff\getKey($diff);
                $name = $name === '' ? $key : $name . "." . $key;
                $margin = getMarginLeft($debt, 4, 2); 
                $diffStatus = \Gendiff\Diff\getDiffStatus($diff);
                $formattedDiff = processDiff($diff, $formatName, $debt, $name);
                $acc  = $acc . $formattedDiff;
                return $acc;
        },'');
  //   if ($formatName === 'stylish' || $formatName === 'json') {
    if ($formatName === 'stylish') {
        $braceMargin = getMarginLeft(($debt - 1),  4, 0);
        $formatted =  "{\n" . $formatted . $braceMargin  ."}\n";
     }

 return $formatted;

}

function processDiff($diff, $formatName, $debt, $name)
{
        
        $diffStatus = \Gendiff\Diff\getDiffStatus($diff);
        switch ($diffStatus) {
                case DiffStatus::noDifference:
                        $output = getFormattedForNoDifference($diff, $formatName, $debt, $name);
                    break;
                case DiffStatus::added:
                        $output = getFormattedForAdded($diff, $formatName, $debt, $name);
                    break;
                case DiffStatus::removed:
                        $output = getFormattedForRemoved($diff, $formatName, $debt, $name);
                    break;
                case DiffStatus::updated:
                        $output = getFormattedForUpdated($diff, $formatName, $debt, $name);
                    break;
                case DiffStatus::parentDiffNode:
                        $output = getFormattedForParentDiffNode($diff, $formatName, $debt, $name); 
                    break;
                default:
                    break;
        }
        return $output;


}

function getFormattedForAdded($diff, $formatName, $debt, $name)
{
        switch ($formatName) {
                case 'plain':
                        $output = getPlainForAdded($diff, $name);
                        break;
                case 'stylish':
                        $output = getNestedFormatForAdded($diff, $debt, $formatName);
                        break;
                case 'json':
                        $output = jsonDumpProperties($diff, $debt); 
                    break;
                default:
                    throw new Exception('Unsupported format');
                }
            return $output;
}

function getFormattedForRemoved($diff, $formatName, $debt, $name)
{
        switch ($formatName) {
                case 'plain':
                        $output = getPlainForRemoved($name);
                        break;
                case 'stylish':
                        $output = getNestedFormatForRemoved($diff, $debt, $formatName);
                        break;
                case 'json':
                        $output = jsonDumpProperties($diff, $debt);
                    break;
                default:
                    throw new Exception('Unsupported format');
                }
            return $output;
}

function getFormattedForUpdated($diff, $formatName, $debt, $name)
{
        switch ($formatName) {
                case 'plain':
                        $output = getPlainForUpdated($diff, $name);
                        break;
                case 'stylish':
                        $output = getNestedFormatForUpdated($diff, $debt, $formatName);
                        break;
                case 'json':
                        $output = jsonDumpProperties($diff, $debt);
                    break;
                default:
                    throw new Exception('Unsupported format');
                }
            return $output;
}

function getFormattedForNoDifference($diff, $formatName, $debt, $name)
{
        switch ($formatName) {
                case 'plain':
                        $output = '';
                        break;
                case 'stylish':
                        $output = getNestedFormatForIdentity($diff, $debt, $formatName);
                        break;
                case 'json':
                        $output = jsonDumpProperties($diff, $debt);
                    break;
                default:
                    throw new Exception('Unsupported format');
                }
            return $output;
}

function getFormattedForParentDiffNode($diff, $formatName, $debt, $name)
{
        switch ($formatName) {
                case 'plain':
                        $child = \Gendiff\Diff\getChild($diff);
                        $output = getFormattedDiffCol($child , 'plain', 1, $name);
                        break;
                case 'stylish':
                        $output = getNestedFormatFromDiffWithChild($diff, $debt, $formatName);
                        break;
                case 'json':
                        $output = jsonDumpProperties($diff, $debt);
                    break;
                default:
                    throw new Exception('Unsupported format');
                }
            return $output;
}


/////////functions for plain formatting

function getPlainForAdded($diff, $propertyName) {
        $value  = \Gendiff\Diff\getNewValue($diff);
        $value = getPlainValueEncode($value);
        return "Property '{$propertyName}' was added with value: {$value}\n";
}


function getPlainForRemoved($propertyName) {

        return "Property '{$propertyName}' was removed\n";
}



function getPlainForUpdated($diff, $propertyName) {
        $value  = \Gendiff\Diff\getValue($diff);
        $value  = getPlainValueEncode($value);
        $newValue = \Gendiff\Diff\getNewValue($diff);
        $newValue = getPlainValueEncode($newValue);
        return "Property '{$propertyName}' was updated. From {$value} to {$newValue}\n";
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


/////////functions for nested formatting (stylish, json)

function getNestedFormatFromDiffWithChild($diff, $debt, $formatName)
{
    $diffStatusSymbol  = '  ';
    $margin = getMarginLeft($debt, 4, 2);
    $childDiffNode = \Gendiff\Diff\getChild($diff);
    $key = getFormattedKey($diff, $formatName);
    $output = $margin . $diffStatusSymbol . $key . ": " . getFormattedDiffCol($childDiffNode, $formatName, ($debt + 1));
    return $output;
}

function getNestedFormatForAdded($diff, $debt, $formatName) {
    $diffStatusSymbol = '+ ';
    $output = getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbol, DiffValues::newValue); 
    return $output;
}
 
function getNestedFormatForRemoved($diff, $debt, $formatName) {
        $diffStatusSymbol  = '- ';
        $output = getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbol, DiffValues::value);
        return $output;
}
 
 
function getNestedFormatForUpdated($diff, $debt, $formatName) {
        $diffStatusSymbolFirst  = '- ';
        $diffStatusSymbolSecond = '+ ';
        $output = getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbolFirst, DiffValues::value);
        $output = $output . getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbolSecond, DiffValues::newValue);
        return $output;  
}
 
 
function getNestedFormatForIdentity($diff, $debt, $formatName) {
        $diffStatusSymbol  = '  ';
        $output = getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbol, DiffValues::value);
        return $output; 
}
 



enum DiffValues
{
    case value;
    case newValue;
}

function getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbol, DiffValues $whichValue)
{
    $key = getFormattedKey($diff, $formatName);
    $margin = getMarginLeft($debt, 4, 2);
    switch ($whichValue) {
        case DiffValues::value:
            $value  = \Gendiff\Diff\getValue($diff);
            $isComplex = \Gendiff\Diff\isFirstValueComplex($diff);
            break;
        case DiffValues::newValue:
            $value  = \Gendiff\Diff\getNewValue($diff);
            $isComplex = \Gendiff\Diff\isSecondValueComplex($diff);
            break;
        default:
            break;
        }
    $value = formatValue($value, $isComplex, ($debt + 1), $formatName);
    $spaceBeforeValue = $value === '' ? '' : ' ';
    $symbolAfterValue = $isComplex ? '' : "\n";
    $output  = $margin . $diffStatusSymbol . $key  . ":" . $spaceBeforeValue . $value  . $symbolAfterValue;
    return $output;
}

function formatValue($value, $isComplexValue, $debt, $formatName, $comma = '')
{      
    switch ($formatName) {
        case 'stylish':
            $value = $isComplexValue ? getNestedFormatFromComplexValue($value, ($debt), $formatName) : getStylishValueEncode($value);    
            break;
        case 'json':
            $value = $isComplexValue ? getNestedFormatFromComplexValue($value, ($debt), $formatName) : json_encode($value);
            break;
        default:
            throw new Exception('Unsupported format');
        }
    return $value;
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


function getNestedFormatFromComplexValue($complexValue, $debt, $formatName, $comma = '')
{
        
        $braceMargin = getMarginLeft(($debt - 1),  4, 0);
        $keys = array_keys($complexValue);
        $lastKey = $keys[count($keys) - 1];
        $elements = array_reduce($keys, function($acc, $key) use ($complexValue, $debt, $formatName, $lastKey) {
                $margin = getMarginLeft($debt, 4, 2);
                $ValueIsArray = is_array($complexValue[$key]);
                $comma = $formatName === 'json' && $key !== $lastKey  ? "," : "";
                $value = formatValue($complexValue[$key], $ValueIsArray, ($debt + 1), $formatName);
                $spaceBeforeValue = mb_strlen($value) > 0 ? ' ' : '';
                $symbolAfterValue = $ValueIsArray ? '' : "\n";
                $key = formatKey($key, $formatName);
                $acc  = $acc . $margin . '  ' . $key . ":" . $spaceBeforeValue . $value . $comma . $symbolAfterValue;
                return $acc;
        },'');
        $output =  "{\n" . $elements . $braceMargin  ."}\n";
        return $output;
}

function formatKey($key, $formatName)
{
    switch ($formatName) {
        case 'stylish':
            break;
        case 'json':
            $key = "\"{$key}\"";
            break;
        default:
            throw new Exception('Unsupported format');
        }
    return $key;
}

function getFormattedKey($diff, $formatName)
{
        $key = \Gendiff\Diff\getKey($diff);
        return formatKey($key, $formatName);
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
    $jsonKey = getFormattedKey($diff, 'json');
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
        $jsonKey = formatValue($key, false, $debt, 'json');
        //$value = $properties[$key];
        //$jsonValue = !is_array($value) ? json_encode($value) : getJsonFromComplexValue($complexValue, $debt, $comma = '');
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

/*
function formatValue($value, $isComplexValue, $debt, $formatName, $comma = '')
{      
    switch ($formatName) {
        case 'stylish':
            $value = $isComplexValue ? getNestedFormatFromComplexValue($value, ($debt), $formatName) : getStylishValueEncode($value);    
            break;
        case 'json':
            $value = $isComplexValue ? getNestedFormatFromComplexValue($value, ($debt), $formatName) : json_encode($value);
            break;
        default:
            throw new Exception('Unsupported format');
        }
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
                $comma = $formatName === 'json' && $key !== $lastKey  ? "," : "";
                $value = formatValue($complexValue[$key], $ValueIsArray, ($debt + 1), $formatName);
                $spaceBeforeValue = mb_strlen($value) > 0 ? ' ' : '';
                $symbolAfterValue = $ValueIsArray ? '' : "\n";
                $key = formatKey($key, $formatName);
                $acc  = $acc . $margin . '  ' . $key . ":" . $spaceBeforeValue . $value . $comma . $symbolAfterValue;
                return $acc;
        },'');
        $output =  "{\n" . $elements . $braceMargin  ."}\n";
        return $output;
}*/