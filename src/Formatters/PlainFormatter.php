<?php

namespace Differ\Formatters\PlainFormatter;

use  Differ\DifferStatus\DiffStatus;
use Exception;

use function Differ\Differ\getDiffStatus;
use function Differ\Differ\getValue;
use function Differ\Differ\getNewValue;
use function Differ\Differ\getChild;

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
    $diffStatus = getDiffStatus($diff);
    switch ($diffStatus) {
        case DiffStatus::noDifference:
            return '';
        case DiffStatus::added:
            $newValue = getNewValue($diff);
            $formattedNewValue = getPlainValueEncode($newValue);
            return "Property '{$propertyName}' was added with value: {$formattedNewValue}\n";
        case DiffStatus::removed:
            return "Property '{$propertyName}' was removed\n";
        case DiffStatus::updated:
            $value  = getValue($diff);
            $formattedValue  = getPlainValueEncode($value);
            $newValue = getNewValue($diff);
            $formattedNewValue = getPlainValueEncode($newValue);
            return "Property '{$propertyName}' was updated. From {$formattedValue} to {$formattedNewValue}\n";
        case DiffStatus::parentDiffNode:
            $child = getChild($diff);
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
            return (bool)$value ? "true" : "false";
        case "NULL":
            return "null";
        case "array":
            return "[complex value]";
        case "string":
            return "'{$value}'";
        default:
            return  $value;
    }
}
