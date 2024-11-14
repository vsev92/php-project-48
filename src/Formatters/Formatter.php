<?php

namespace Differ\DiffColFormatter;

use Exception;

use function Differ\Formatters\PlainFormatter\plainDumpDiffCol;
use function Differ\Formatters\StylishFormatter\stylishDumpDiffCol;
use function Differ\Formatters\JsonFormatter\jsonDumpDiffCol;

function getFormattedDiffCol(array $diffCol, string $formatName)
{
    switch ($formatName) {
        case 'plain':
            $output = plainDumpDiffCol($diffCol);
            break;
        case 'stylish':
            $output = stylishDumpDiffCol($diffCol);
            break;
        case 'json':
            $output = jsonDumpDiffCol($diffCol);
            break;
        default:
            throw new Exception('Unsupported format');
    }
    return $output;
}
