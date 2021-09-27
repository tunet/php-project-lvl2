<?php

namespace Differ\Differ;

use function Differ\Formatters\Formatters\getFormatter;
use function Differ\Parsers\getParser;
use function Differ\Tree\createChangedNode;
use function Differ\Tree\createNode;
use function Differ\Tree\getTypeIfObject;
use function Functional\sort;

use const Differ\Formatters\Formatters\FORMATTER_STYLISH;
use const Differ\Tree\OPERATION_ADDED;
use const Differ\Tree\OPERATION_NOT_CHANGED;
use const Differ\Tree\OPERATION_REMOVED;
use const Differ\Tree\TYPE_OBJECT;
use const PATHINFO_EXTENSION;

function genDiff(string $filePath1, string $filePath2, string $format = FORMATTER_STYLISH): string
{
    $ast = getAstFromFiles($filePath1, $filePath2);
    $formatter = getFormatter($format);

    return $formatter($ast);
}

function getAstFromFiles(string $filePath1, string $filePath2): array
{
    $data1 = getDataFromFile($filePath1);
    $data2 = getDataFromFile($filePath2);

    return getAst($data1, $data2);
}

function getDataFromFile(string $filePath): array
{
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $parser = getParser($extension);
    $content = file_get_contents($filePath);

    return $parser($content);
}

function getAst(array $data1, array $data2): array
{
    $func = function (array $data1, array $data2) use (&$func): array {
        $keys = array_merge(array_keys($data1), array_keys($data2));
        $uniqueKeys = array_unique($keys);
        $sortedKeys = sort($uniqueKeys, fn($key1, $key2) => $key1 <=> $key2);

        return array_reduce($sortedKeys, function (array $acc, $key) use ($func, $data1, $data2): array {
            $isFirstExists = array_key_exists($key, $data1);
            $isSecondExists = array_key_exists($key, $data2);

            $isFirstObject = $isFirstExists && is_array($data1[$key]);
            $isSecondObject = $isSecondExists && is_array($data2[$key]);

            if ($isFirstObject && $isSecondObject) {
                return [
                    ...$acc,
                    createNode(
                        TYPE_OBJECT,
                        OPERATION_NOT_CHANGED,
                        $key,
                        $func($data1[$key], $data2[$key]),
                    ),
                ];
            }

            if ($isFirstExists && $isSecondExists && $data1[$key] !== $data2[$key]) {
                $value1 = $isFirstObject ? $func($data1[$key], $data1[$key]) : $data1[$key];
                $value2 = $isSecondObject ? $func($data2[$key], $data2[$key]) : $data2[$key];

                return [
                    ...$acc,
                    createChangedNode(
                        getTypeIfObject($isFirstObject),
                        getTypeIfObject($isSecondObject),
                        $key,
                        $value1,
                        $value2,
                    ),
                ];
            }

            if ($isFirstExists && $isSecondExists) {
                $value = $isFirstObject ? $func($data1[$key], $data1[$key]) : $data1[$key];

                return [...$acc, createNode(getTypeIfObject($isFirstObject), OPERATION_NOT_CHANGED, $key, $value)];
            }

            if ($isFirstExists) {
                $value = $isFirstObject ? $func($data1[$key], $data1[$key]) : $data1[$key];

                return [...$acc, createNode(getTypeIfObject($isFirstObject), OPERATION_REMOVED, $key, $value)];
            }

            $value = $isSecondObject ? $func($data2[$key], $data2[$key]) : $data2[$key];

            return [...$acc, createNode(getTypeIfObject($isSecondObject), OPERATION_ADDED, $key, $value)];
        }, []);
    };

    return $func($data1, $data2);
}
