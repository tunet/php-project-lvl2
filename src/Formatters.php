<?php

namespace Differ\Formatters;

use InvalidArgumentException;

const FORMATTER_STYLISH = 'stylish';
const FORMATTER_PLAIN = 'plain';
const FORMATTER_JSON = 'json';

function getFormatter(string $format): string
{
    return match (strtolower($format)) {
        FORMATTER_STYLISH => 'Differ\Formatters\Stylish',
        FORMATTER_PLAIN => 'Differ\Formatters\Plain',
        FORMATTER_JSON => 'Differ\Formatters\Json',
        default => throw new InvalidArgumentException("Format {$format} is not supported"),
    };
}
