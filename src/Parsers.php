<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;
use InvalidArgumentException;
use Exception;

enum SourceType
{
    case json;
    case yaml;
    case unsupported;
}

function parseFromFile(string $filePath, SourceType $sourceFileType)
{
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        switch ($sourceFileType) {
            case SourceType::json:
                return json_decode($data, true);
            case SourceType::yaml:
                return Yaml::parse($data);
            default:
                throw new InvalidArgumentException('wrong file extension' . $filePath);
        }
    } else {
        throw new InvalidArgumentException('wrong filename ' . $filePath);
    }
}

function getSourceType($filePath)
{
    if (str_ends_with($filePath, '.json')) {
        return SourceType::json;
    } elseif (str_ends_with($filePath, '.yml') || str_ends_with($filePath, '.yaml')) {
        return SourceType::yaml;
    } else {
        throw new Exception('Unsupported file type' . $filePath);
    }
}
