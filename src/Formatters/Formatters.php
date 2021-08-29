<?php

namespace Formatters\Formatters;

use InvalidArgumentException;

use function Formatters\Stylish\format;
use function strtolower;

const FORMATTER_STYLISH = 'stylish';

function getFormatter(string $format): callable
{
    return match (strtolower($format)) {
        FORMATTER_STYLISH => fn(array $ast) => format($ast),
        default => throw new InvalidArgumentException("Format {$format} is not supported"),
    };
}
