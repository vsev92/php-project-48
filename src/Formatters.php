<?php

namespace Gendiff\Formatters;
use Gendiff\Diff;
use Gendiff\Diff\PropertyDifference;


function getStylishFromFirstValueOfDiff($diff, $diffStatusSymbol, $debt) {
        $key = \Gendiff\Diff\getKey($diff);
        $margin = getMarginLeft($debt, 4, 2);
        $value  = \Gendiff\Diff\getFirstValue($diff);
        $spaceBeforeValue = $value === '' ? '' : ' ';
        $isComplex = \Gendiff\Diff\isFirstValueComplex($diff);
        $stylishValue = $isComplex ? getStylishFromComplexValue($value, ($debt + 1)) : getStylishValueEncode($value);
        $symbolAfterValue = $isComplex ? '' : "\n";
        $stylish  = $margin . $diffStatusSymbol . $key  . ":" . $spaceBeforeValue . $stylishValue  . $symbolAfterValue;
        return $stylish;

}

function getStylishFromSecondValueOfDiff($diff, $diffStatusSymbol, $debt) {
        $key = \Gendiff\Diff\getKey($diff);
        $margin = getMarginLeft($debt, 4, 2);
        $value  = \Gendiff\Diff\getSecondValue($diff);
        $spaceBeforeValue = $value === '' ? '' : ' ';
        $isComplex = \Gendiff\Diff\isSecondValueComplex($diff);
        $stylishValue = $isComplex ? getStylishFromComplexValue($value, ($debt + 1)) : getStylishValueEncode($value);
        $symbolAfterValue = $isComplex ? '' : "\n";
        $stylish  = $margin . $diffStatusSymbol . $key  . ":" . $spaceBeforeValue . $stylishValue  . $symbolAfterValue;
        return $stylish;

}


function getStylishForAdded($diff, $debt) {
        $diffStatusSymbol = '+ ';
        $stylish  = getStylishFromSecondValueOfDiff($diff, $diffStatusSymbol, $debt);
        return $stylish;
}

function getStylishForRemoved($diff, $debt) {
        $diffStatusSymbol  = '- ';
        $stylish  = getStylishFromFirstValueOfDiff($diff, $diffStatusSymbol, $debt);
        return $stylish;
}


function getStylishForUpdated($diff, $debt) {
        $diffStatusSymbolFirst  = '- ';
        $diffStatusSymbolSecond = '+ ';
        $stylish  = getStylishFromFirstValueOfDiff($diff, $diffStatusSymbolFirst, $debt);
        $stylish  = $stylish . getStylishFromSecondValueOfDiff($diff, $diffStatusSymbolSecond, $debt);
        return $stylish;  
}


function getStylishForIdentity($diff, $debt) {
        $diffStatusSymbol  = '  ';
        $stylish  = getStylishFromFirstValueOfDiff($diff, $diffStatusSymbol, $debt);
        return $stylish; 
}

function getStylishFromDiffWithChild($diff, $debt) {
        $diffStatusSymbol  = '  ';
        $margin = getMarginLeft($debt, 4, 2);
        $childDiffNode = \Gendiff\Diff\getChild($diff);
        $key = \Gendiff\Diff\getKey($diff);
        $stylish = $margin . $diffStatusSymbol . $key . ": " . getStylishFromDiffCol($childDiffNode, ($debt + 1));
        return $stylish; 

}


function getStylishFromComplexValue($complexValue, $debt) {

        $braceMargin = getMarginLeft(($debt - 1),  4, 0);

        $keys = array_keys($complexValue);
        $elements = array_reduce($keys, function($acc, $key) use ($complexValue, $debt) {
               
 
                
                $margin = getMarginLeft($debt, 4, 2);
                $ValueIsArray = is_array($complexValue[$key]);
                $value = $ValueIsArray ? getStylishFromComplexValue($complexValue[$key], ($debt+1)) : getStylishValueEncode($complexValue[$key]);
                $spaceBeforeValue = mb_strlen($value) > 0 ? ' ' : '';
                $symbolAfterValue = $ValueIsArray ? '' : "\n";

                $acc  = $acc . $margin . '  ' . $key . ":" . $spaceBeforeValue . $value . $symbolAfterValue;
               

                return $acc;
        },'');
        $stylish =  "{\n" . $elements . $braceMargin  ."}\n";
        return $stylish;
}




function getStylishFromDiffCol($diffCol, $debt) {

        //var_dump($diffCol);

        $braceMargin = getMarginLeft(($debt - 1),  4, 0);
        $elements = array_reduce($diffCol, function($acc, $diff) use ($debt) {
               
 
                
                $margin = getMarginLeft($debt, 4, 2);

                 
                $diffStatus = \Gendiff\Diff\getPropertyDifference($diff);
                $diffStatusSymbol = '';
                $diffStatusSymbolFirst  = '';
                $diffStatusSymbolSecond = '';
                switch ($diffStatus) {
                        case PropertyDifference:: none:
                            $stylish = getStylishForIdentity($diff, $debt);
                            break;
                        case PropertyDifference::added:
                            $stylish = getStylishForAdded($diff, $debt);
                            break;
                        case PropertyDifference::removed:
                            $stylish = getStylishForRemoved($diff, $debt);
                            break;
                        case PropertyDifference::updated:
                            $stylish = getStylishForUpdated($diff, $debt);
                            break;
                        case PropertyDifference::complexDifference:
                            $stylish = getStylishFromDiffWithChild($diff, $debt);
                            break;
                        default:
                            break;
                    }

                       $acc  = $acc . $stylish;
                       return $acc;
        },'');

        $stylish =  "{\n" . $elements . $braceMargin  ."}\n";
        return $stylish;
}




function getMarginLeft($debt, $spaceCountPerLevel, $offsetToLeft) {
        $repeatCount = $debt * $spaceCountPerLevel - $offsetToLeft;
        $repeatCount = $repeatCount < 0 ? 0 : $repeatCount;
        return str_repeat(" ", $repeatCount);

}

function getStylishValueEncode($value) {
        $type = gettype($value);
        switch ($type) {
                case "boolean":
                    return $value ? "true" : "false";
                    break;
                case "NULL":
                    return "null";
                    break;
                default:
                    return  $value;
                    break;
            }
   

}


///////////////////////////////////PLAIN




function getPlainFromDiffCol($diffCol, $name) {
        $plain = array_reduce($diffCol, function($acc, $diff) use ($name) {
                $name = $name . "." . \Gendiff\Diff\getKey($diff);
                $diffStatus = \Gendiff\Diff\getPropertyDifference($diff);
                switch ($diffStatus) {
                        case PropertyDifference:: none:
                            break;
                        case PropertyDifference::added:
                            $output = getPlainForAdded($diff, $name);
                            break;
                        case PropertyDifference::removed:
                            $output = getPlainForRemoved($name);
                            break;
                        case PropertyDifference::updated:
                            $output= getPlainForUpdated($diff, $name);
                            break;
                        case PropertyDifference::complexDifference:
                            $child = \Gendiff\Diff\getChild($diff);
                            $output = getPlainFromDiffCol($child, $name); 
                            break;
                        default:
                            break;
                    }

                       $acc  = $acc . $output;
                       return $acc;
        },'');

        return $plain;
}


function getPlainForAdded($diff, $propertyName) {

        $value  = \Gendiff\Diff\getSecondValue($diff);
        return "Property {$propertyName} was added with value: {$value}\n";
}

function getPlainForRemoved($propertyName) {

        return "Property {$propertyName} was removed.\n";
}



function getPlainForUpdated($diff, $propertyName) {
        $value1  = \Gendiff\Diff\getFirstValue($diff);
        $value2 = \Gendiff\Diff\getSecondValue($diff);
        return "Property {$propertyName} was updated. From {$value1} to {$value2}\n";
}


