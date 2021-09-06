<?php

namespace Formatters\Formatters;

use InvalidArgumentException;

use function Formatters\Plain\format as plainFormat;
use function Formatters\Stylish\format as stylishFormat;
use function json_encode;
use function strtolower;

use const JSON_PRETTY_PRINT;

const FORMATTER_STYLISH = 'stylish';
const FORMATTER_PLAIN = 'plain';
const FORMATTER_JSON = 'json';

function getFormatter(string $format): callable
{
    return match (strtolower($format)) {
        FORMATTER_STYLISH => fn(array $ast) => stylishFormat($ast),
        FORMATTER_PLAIN => fn(array $ast) => plainFormat($ast),
        FORMATTER_JSON => fn(array $ast) => json_encode($ast, JSON_PRETTY_PRINT),
        default => throw new InvalidArgumentException("Format {$format} is not supported"),
    };
}
