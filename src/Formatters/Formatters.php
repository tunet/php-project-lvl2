<?php

namespace Differ\Formatters\Formatters;

use InvalidArgumentException;

use function Differ\Formatters\Json\render as jsonFormat;
use function Differ\Formatters\Plain\render as plainFormat;
use function Differ\Formatters\Stylish\render as stylishFormat;

const FORMATTER_STYLISH = 'stylish';
const FORMATTER_PLAIN = 'plain';
const FORMATTER_JSON = 'json';

function getFormatter(string $format): callable
{
    return match (strtolower($format)) {
        FORMATTER_STYLISH => fn(array $ast) => stylishFormat($ast),
        FORMATTER_PLAIN => fn(array $ast) => plainFormat($ast),
        FORMATTER_JSON => fn(array $ast) => jsonFormat($ast),
        default => throw new InvalidArgumentException("Format {$format} is not supported"),
    };
}
