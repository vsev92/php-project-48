<?php

namespace Gendiff\Parser;
use InvalidArgumentException;

function parseJsonFile(string $path)
{
    if (file_exists($path)) {
        $json = file_get_contents($path);
        return json_decode($json, true);
    } else {
        throw new InvalidArgumentException('wrong filename ' . $path);
    }
    
}

