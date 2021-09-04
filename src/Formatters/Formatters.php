<?php

namespace Formatters\Formatters;

use InvalidArgumentException;

use function Formatters\Plain\format as plainFormat;
use function Formatters\Stylish\format as stylishFormat;
use function strtolower;

const FORMATTER_STYLISH = 'stylish';
const FORMATTER_PLAIN = 'plain';

function getFormatter(string $format): callable
{
    return match (strtolower($format)) {
        FORMATTER_STYLISH => fn(array $ast) => stylishFormat($ast),
        FORMATTER_PLAIN => fn(array $ast) => plainFormat($ast),
        default => throw new InvalidArgumentException("Format {$format} is not supported"),
    };
}
