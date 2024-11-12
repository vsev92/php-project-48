<?php

namespace Differ\Formatters\JsonFormatter;

use Exception;

function jsonDumpDiffCol(array $diffCol)
{
    return json_encode($diffCol, JSON_PRETTY_PRINT);
}
