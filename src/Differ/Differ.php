<?php

namespace Differ\Differ;

use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_reduce;
use function array_unique;
use function file_get_contents;
use function Formatters\Formatters\getFormatter;
use function is_array;
use function Parsers\Parsers\getParser;
use function sort;

use const Formatters\Formatters\FORMATTER_STYLISH;
use const PATHINFO_EXTENSION;

function genDiff(string $filePath1, string $filePath2, string $format = FORMATTER_STYLISH): string
{
    $ast = getAstByFiles($filePath1, $filePath2);
    $formatter = getFormatter($format);

    return $formatter($ast);
}

function getAstByFiles(string $filePath1, string $filePath2): array
{
    $data1 = getFileData($filePath1);
    $data2 = getFileData($filePath2);

    return getAstByData($data1, $data2);
}

function getAstByData(array $data1, array $data2): array
{
    $func = function (array $data1, array $data2) use (&$func): array {
        $keys = array_merge(array_keys($data1), array_keys($data2));
        $keys = array_unique($keys);
        sort($keys);

        return array_reduce($keys, function (array $acc, $key) use ($data1, $data2, $func): array {
            $isFirstExists = array_key_exists($key, $data1);
            $isSecondExists = array_key_exists($key, $data2);
            $isFirstObject = $isFirstExists && is_array($data1[$key]);
            $isSecondObject = $isSecondExists && is_array($data2[$key]);

            if ($isFirstExists && $isSecondExists && $isFirstObject && $isSecondObject) {
                $acc[] = [
                    'operation' => 'not_changed',
                    'type'      => 'object',
                    'key'       => $key,
                    'value'     => $func($data1[$key], $data2[$key]),
                ];

                return $acc;
            }

            if ($isFirstExists && $isSecondExists && $isFirstObject && !$isSecondObject) {
                $acc[] = [
                    'operation' => 'changed',
                    'oldType'   => 'object',
                    'newType'   => 'simple',
                    'key'       => $key,
                    'oldValue'  => $func($data1[$key], $data1[$key]),
                    'newValue'  => $data2[$key],
                ];

                return $acc;
            }

            if ($isFirstExists && $isSecondExists && !$isFirstObject && $isSecondObject) {
                $acc[] = [
                    'operation' => 'changed',
                    'oldType'   => 'simple',
                    'newType'   => 'object',
                    'key'       => $key,
                    'oldValue'  => $data1[$key],
                    'newValue'  => $func($data2[$key], $data2[$key]),
                ];

                return $acc;
            }

            if (!$isFirstExists && $isSecondExists && $isSecondObject) {
                $acc[] = [
                    'operation' => 'added',
                    'type'      => 'object',
                    'key'       => $key,
                    'value'     => $func($data2[$key], $data2[$key]),
                ];

                return $acc;
            }

            if ($isFirstExists && !$isSecondExists && $isFirstObject) {
                $acc[] = [
                    'operation' => 'removed',
                    'type'      => 'object',
                    'key'       => $key,
                    'value'     => $func($data1[$key], $data1[$key]),
                ];

                return $acc;
            }

            if (!$isFirstExists && $isSecondExists) {
                $acc[] = [
                    'operation' => 'added',
                    'type'      => 'simple',
                    'key'       => $key,
                    'value'     => $data2[$key],
                ];

                return $acc;
            }

            if ($isFirstExists && !$isSecondExists) {
                $acc[] = [
                    'operation' => 'removed',
                    'type'      => 'simple',
                    'key'       => $key,
                    'value'     => $data1[$key],
                ];

                return $acc;
            }

            if ($data1[$key] === $data2[$key]) {
                $acc[] = [
                    'operation' => 'not_changed',
                    'type'      => 'simple',
                    'key'       => $key,
                    'value'     => $data1[$key],
                ];

                return $acc;
            }

            $acc[] = [
                'operation' => 'changed',
                'oldType'   => 'simple',
                'newType'   => 'simple',
                'key'       => $key,
                'oldValue'  => $data1[$key],
                'newValue'  => $data2[$key],
            ];

            return $acc;
        }, []);
    };

    return $func($data1, $data2);
}

function getFileData(string $filePath): array
{
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $parser = getParser($extension);
    $content = file_get_contents($filePath);

    return $parser($content);
}
