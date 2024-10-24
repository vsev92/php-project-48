<?php

namespace Gendiff\Formatters;

use Gendiff\Diff;
use Gendiff\Diff\PropertyDifference;
use Exception;

function getFormattedDifference($diffCol, $formatName) {
        switch ($formatName) {
                case "plain":
                    return getPlainFromDiffCol($diffCol, "");
                case "stylish":
                    return getNestedFormatFromDiffCol($diffCol, 'stylish', 1);
                case "json":
                        return getNestedFormatFromDiffCol($diffCol, 'json', 1);
                default:
                    throw new InvalidArgumentException('Unsupported format name');
        }
}  



/////////functions for plain formatting

function getPlainFromDiffCol($diffCol, $name) {
        $plain = array_reduce($diffCol, function($acc, $diff) use ($name) {
                $key = \Gendiff\Diff\getKey($diff);
                $name = $name === '' ? $key : $name . "." . $key;
                $diffStatus = \Gendiff\Diff\getPropertyDifference($diff);
                $plain = '';
                switch ($diffStatus) {
                        case PropertyDifference:: none:
                            break;
                        case PropertyDifference::added:
                            $plain = getPlainForAdded($diff, $name);
                            break;
                        case PropertyDifference::removed:
                            $plain = getPlainForRemoved($name);
                            break;
                        case PropertyDifference::updated:
                            $plain= getPlainForUpdated($diff, $name);
                            break;
                        case PropertyDifference::complexDifference:
                            $child = \Gendiff\Diff\getChild($diff);
                            $plain = getPlainFromDiffCol($child, $name); 
                            break;
                        default:
                            break;
                    }

                       $acc  = $acc . $plain;
                       return $acc;
        },'');
        return $plain;
}


function getPlainForAdded($diff, $propertyName) {
        $value  = \Gendiff\Diff\getSecondValue($diff);
        $value = getPlainValueEncode($value);
        return "Property '{$propertyName}' was added with value: {$value}\n";
}

function getPlainForRemoved($propertyName) {

        return "Property '{$propertyName}' was removed\n";
}



function getPlainForUpdated($diff, $propertyName) {
        $value1  = \Gendiff\Diff\getFirstValue($diff);
        $value1 = getPlainValueEncode($value1);
        $value2 = \Gendiff\Diff\getSecondValue($diff);
        $value2 = getPlainValueEncode($value2);
        return "Property '{$propertyName}' was updated. From {$value1} to {$value2}\n";
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
function getNestedFormatFromDiffCol($diffCol, $formatName, $debt) {
    $braceMargin = getMarginLeft(($debt - 1),  4, 0);
    $elements = array_reduce($diffCol, function($acc, $diff) use ($formatName, $debt) {
        $margin = getMarginLeft($debt, 4, 2); 
        $diffStatus = \Gendiff\Diff\getPropertyDifference($diff);
        switch ($diffStatus) {
            case PropertyDifference:: none:
                $output = getNestedFormatForIdentity($diff, $debt, $formatName);
                break;
            case PropertyDifference::added:
                $output = getNestedFormatForAdded($diff, $debt, $formatName);
                break;
            case PropertyDifference::removed:
                $output = getNestedFormatForRemoved($diff, $debt, $formatName);
                break;
            case PropertyDifference::updated:
                $output = getNestedFormatForUpdated($diff, $debt, $formatName);
                break;
            case PropertyDifference::complexDifference:
                $output = getNestedFormatFromDiffWithChild($diff, $debt, $formatName);
                break;
            default:
                break;
        }

                $acc  = $acc . $output;
                return $acc;
        },'');
        $output =  "{\n" . $elements . $braceMargin  ."}\n";
        return $output;
}

function getNestedFormatFromDiffWithChild($diff, $debt, $formatName)
{
    $diffStatusSymbol  = '  ';
    $margin = getMarginLeft($debt, 4, 2);
    $childDiffNode = \Gendiff\Diff\getChild($diff);
    $key = getFormattedKey($diff, $formatName);
    $output = $margin . $diffStatusSymbol . $key . ": " . getNestedFormatFromDiffCol($childDiffNode, $formatName, ($debt + 1));
    return $output;
}

function getNestedFormatForAdded($diff, $debt, $formatName) {
    $diffStatusSymbol = '+ ';
    $output = getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbol, DiffValues::second); 
    return $output;
}
 
function getNestedFormatForRemoved($diff, $debt, $formatName) {
        $diffStatusSymbol  = '- ';
        $output = getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbol, DiffValues::first);
        return $output;
}
 
 
function getNestedFormatForUpdated($diff, $debt, $formatName) {
        $diffStatusSymbolFirst  = '- ';
        $diffStatusSymbolSecond = '+ ';
        $output = getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbolFirst, DiffValues::first);
        $output = $output . getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbolSecond, DiffValues::second);
        return $output;  
}
 
 
function getNestedFormatForIdentity($diff, $debt, $formatName) {
        $diffStatusSymbol  = '  ';
        $output = getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbol, DiffValues::first);
        return $output; 
}
 

enum DiffValues
{
    case first;
    case second;
}

function getNestedFormatString($diff, $debt, $formatName, $diffStatusSymbol, DiffValues $whichValue)
{
    $key = getFormattedKey($diff, $formatName);
    $margin = getMarginLeft($debt, 4, 2);
    switch ($whichValue) {
        case DiffValues::first:
            $value  = \Gendiff\Diff\getFirstValue($diff);
            $isComplex = \Gendiff\Diff\isFirstValueComplex($diff);
            break;
        case DiffValues::second:
            $value  = \Gendiff\Diff\getSecondValue($diff);
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

function formatValue($value, $isComplexValue, $debt, $formatName)
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


function getNestedFormatFromComplexValue($complexValue, $debt, $formatName)
{
        $braceMargin = getMarginLeft(($debt - 1),  4, 0);
        $keys = array_keys($complexValue);
        $elements = array_reduce($keys, function($acc, $key) use ($complexValue, $debt, $formatName) {
                $margin = getMarginLeft($debt, 4, 2);
                $ValueIsArray = is_array($complexValue[$key]);
                $value = formatValue($complexValue[$key], $ValueIsArray, ($debt + 1), $formatName);
                $spaceBeforeValue = mb_strlen($value) > 0 ? ' ' : '';
                $symbolAfterValue = $ValueIsArray ? '' : "\n";
                $key = formatKey($key, $formatName);
                $acc  = $acc . $margin . '  ' . $key . ":" . $spaceBeforeValue . $value . $symbolAfterValue;
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




 


////////////////////



