<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use  Differ\FileFormat\FileFormat;
use InvalidArgumentException;
use Exception;

function parse(string $data, FileFormat $format)
{

    switch ($format) {
        case FileFormat::json:
            return json_decode($data, true);
        case FileFormat::yaml:
            return Yaml::parse($data);
    }
}
