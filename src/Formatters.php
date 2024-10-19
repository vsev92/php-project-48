<?php

namespace Gendiff\Formatters;
use  Symfony\Component\Yaml\Yaml;






function getStylishFromDiffCol($diffCol, $debt) {

        $braceMargin = getMarginLeft(($debt - 1),  4, 0);
        $elements = array_reduce($diffCol, function($acc, $diff) use ($debt) {
               
               
                
                $margin = getMarginLeft($debt, 4, 2);
                if (is_array($diff['value'])) {
                       
                        $acc = $acc . $margin . $diff['sign'] . $diff['key'] . ": " . getStylishFromDiffCol($diff['value'], ($debt+1));
    
                                
                } else {

                        
                        $acc  = $acc . $margin . $diff['sign'] . $diff['key'] . ": " . Yaml::dump($diff['value']) . "\n";
                }
                
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

/*
function getStylishFromRawCol($collection, $debt) 
{
        
        $braceMargin = getMarginLeft(($debt - 1),  4, 0);
        $keys = array_keys($collection);
        $elements = array_reduce($keys, function($acc, $key) use ($collection, $debt) {
               
               
                
                $margin = getMarginLeft($debt, 4, 2);
                if (is_array($collection[$key])) {
                       
                        $acc = $acc . $margin . "  " . $key . ": " . getStylishFromCol($collection[$key], ($debt+1));
    
                                
                } else {

                        
                        $acc  = $acc . $margin . "  " . $key . ": " . Yaml::dump($collection[$key]) . "\n";
                }
                
                return $acc;
        },'');
        $stylish =  "{\n" . $elements . $braceMargin  ."}\n";
        return $stylish;

}
*/






