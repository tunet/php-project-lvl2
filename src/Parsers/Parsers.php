<?php

namespace Parsers\Parsers;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

function getParser(string $format): callable
{
    return match (strtolower($format)) {
        'yml', 'yaml' => fn(string $yaml) => parseYaml($yaml),
        'json' => fn(string $json) => parseJson($json),
        default => throw new InvalidArgumentException("Format {$format} is not supported"),
    };
}

function parseYaml(string $yaml): array
{
    return Yaml::parse($yaml);
}

function parseJson(string $json): array
{
    $data = json_decode($json, true);

    if (!is_array($data)) {
        throw new RuntimeException('Json parse error');
    }

    return $data;
}
