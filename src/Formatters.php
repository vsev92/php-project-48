<?php

namespace Gendiff\Formatters;







function getStylishFromDiffCol($diffCol, $debt) {

        $braceMargin = getMarginLeft(($debt - 1),  4, 0);
        $elements = array_reduce($diffCol, function($acc, $diff) use ($debt) {
               
 
                
                $margin = getMarginLeft($debt, 4, 2);
                $ValueIsArray = is_array($diff['value']);
                $value = $ValueIsArray ? getStylishFromDiffCol($diff['value'], ($debt+1)) : getStylishValueEncode($diff['value']);
                $spaceBeforeValue = mb_strlen($value) > 0 ? ' ' : '';
                $symbolAfterValue = $ValueIsArray ? '' : "\n";

                $acc  = $acc . $margin . $diff['sign'] . $diff['key'] . ":" . $spaceBeforeValue . $value . $symbolAfterValue;
               

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








