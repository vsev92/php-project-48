<?php

namespace Gendiff\Formatters;
use  Symfony\Component\Yaml\Yaml;




function getStylishFormat($firstIteration, $name, $diffCol, $debt, $spaceCountPerLevel, $offsetToLeft) {
        //$debt = 1;
        //$spaceCountPerLevel = 4;
        //$offsetToLeft = 2;
       
        $name = $name ==='' ? $name : $name . ": ";
        $elements = array_reduce($diffCol, function($acc, $diff) use ($debt, $spaceCountPerLevel, $offsetToLeft) {
               
               
                $elementMarginLeft = getMarginLeft($debt, $spaceCountPerLevel, $offsetToLeft);
             
                if (is_array($diff['value'])) {
                        
                        $debtOnNextLevel = $debt + 1;
                        $spaceCountOnNextLevel = $spaceCountPerLevel + 4;
                        $offsetToLeft = $firstIteration ? $offsetToLeft + 2 : $offsetToLeft;
        
                        $acc = $acc . getStylishFormat(false, $diff['key'], $diff['value'], $debtOnNextLevel, $spaceCountOnNextLevel, $offsetToLeft);                       
                } else {
                        $acc  = $acc . $elementMarginLeft . $diff['sign'] . $diff['key']. ": " . Yaml::dump($diff['value']) . "\n";
                }
                
                return $acc;
        },'');
        
        $openBraceMarginLeft = getMarginLeft($debt, $spaceCountPerLevel, $offsetToLeft);
        $closeBraceMarginLeft = getMarginLeft($debt, $spaceCountPerLevel, $offsetToLeft);
        if ($firstIteration) {
                $stylish ="{\n" .  $elements . "}";

        } else {
                $stylish = $openBraceMarginLeft . $name . "{\n" .  $elements . $closeBraceMarginLeft . "}\n";
        }
        
        return $stylish;


}

function getStylishFromDiff($diffCol, $debt, $spaceCountPerLevel, $offsetToLeft) {

       
        $name = $name ==='' ? $name : $name . ": ";
        $elements = array_reduce($diffCol, function($acc, $diff) use ($debt, $spaceCountPerLevel, $offsetToLeft) {
               
               
                $elementMarginLeft = getMarginLeft($debt, $spaceCountPerLevel, $offsetToLeft);
             
                if (is_array($diff['value'])) {
                        
                        $debtOnNextLevel = $debt + 1;
                        $spaceCountOnNextLevel = $spaceCountPerLevel + 4;
                        $offsetToLeft = $firstIteration ? $offsetToLeft + 2 : $offsetToLeft;
        
                        $acc = $acc . getStylishFormat(false, $diff['key'], $diff['value'], $debtOnNextLevel, $spaceCountOnNextLevel, $offsetToLeft);                       
                } else {
                        $acc  = $acc . $elementMarginLeft . $diff['sign'] . $diff['key']. ": " . Yaml::dump($diff['value']) . "\n";
                }
                
                return $acc;
        },'');
        
        $openBraceMarginLeft = getMarginLeft($debt, $spaceCountPerLevel, $offsetToLeft);
        $closeBraceMarginLeft = getMarginLeft($debt, $spaceCountPerLevel, $offsetToLeft);
        if ($firstIteration) {
                $stylish ="{\n" .  $elements . "}";

        } else {
                $stylish = $openBraceMarginLeft . $name . "{\n" .  $elements . $closeBraceMarginLeft . "}\n";
        }
        
        return $stylish;


}


function getMarginLeft($debt, $spaceCountPerLevel, $offsetToLeft) {
        $repeatCount = $debt * $spaceCountPerLevel - $offsetToLeft;
        $repeatCount = $repeatCount < 0 ? 0 : $repeatCount;
        return str_repeat(" ", $repeatCount);

}


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







