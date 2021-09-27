<?php

namespace Differ\Formatters\Json;

use RuntimeException;

use const JSON_ERROR_NONE;
use const JSON_PRETTY_PRINT;

function render(array $ast): string
{
    $content = json_encode($ast, JSON_PRETTY_PRINT);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new RuntimeException(json_last_error_msg());
    }

    return $content;
}
