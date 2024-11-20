<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use  Differ\Differ\SourceType;
use InvalidArgumentException;
use Exception;

const JSON_DECODE_ASSOC = true;

function parse(string $sourceData, SourceType $sourceFileType)
{

    switch ($sourceFileType) {
        case SourceType::json:
            return json_decode($sourceData, JSON_DECODE_ASSOC);
        case SourceType::yaml:
            return Yaml::parse($sourceData);
        default:
            throw new Exception('unsupported file type');
    }
}
