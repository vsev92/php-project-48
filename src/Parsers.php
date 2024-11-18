<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use InvalidArgumentException;
use Exception;

enum SourceType
{
    case json;
    case yaml;
}

const JSON_DECODE_ASSOC = true;

function parseFromFile(string $filePath, SourceType $sourceFileType)
{
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        switch ($sourceFileType) {
            case SourceType::json:
                return json_decode((string)$data, JSON_DECODE_ASSOC);
            case SourceType::yaml:
                return Yaml::parse((string)$data);
            default:
                throw new InvalidArgumentException('wrong file extension' . $filePath);
        }
    } else {
        throw new InvalidArgumentException('wrong filename ' . $filePath);
    }
}

function getSourceType(string $filePath)
{
    $pathInfo = pathinfo($filePath);
    $extension = $pathInfo['extension'];
    switch ($extension) {
        case 'json':
            return SourceType::json;
        case 'yaml':
            return SourceType::yaml;
        case 'yml':
            return SourceType::yaml;
        default:
            throw new InvalidArgumentException('wrong file extension' . $filePath);
    }
}
