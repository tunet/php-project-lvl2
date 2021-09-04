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
use function pathinfo;
use function sort;
use function Util\Tree\createChangedNode;
use function Util\Tree\createNode;
use function Util\Tree\getTypeIfObject;

use const Formatters\Formatters\FORMATTER_STYLISH;
use const PATHINFO_EXTENSION;
use const Util\Tree\OPERATION_ADDED;
use const Util\Tree\OPERATION_NOT_CHANGED;
use const Util\Tree\OPERATION_REMOVED;
use const Util\Tree\ROOT_PATH;
use const Util\Tree\TYPE_OBJECT;

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
    $func = function (array $data1, array $data2, string $parent) use (&$func): array {
        $keys = array_merge(array_keys($data1), array_keys($data2));
        $keys = array_unique($keys);
        sort($keys);

        return array_reduce($keys, function (array $acc, $key) use ($func, $parent, $data1, $data2): array {
            $isFirstExists = array_key_exists($key, $data1);
            $isSecondExists = array_key_exists($key, $data2);

            $isFirstObject = $isFirstExists && is_array($data1[$key]);
            $isSecondObject = $isSecondExists && is_array($data2[$key]);

            $newParent = ROOT_PATH === $parent ? $key : "{$parent}.{$key}";

            if ($isFirstObject && $isSecondObject) {
                $acc[] = createNode(
                    TYPE_OBJECT,
                    OPERATION_NOT_CHANGED,
                    $parent,
                    $key,
                    $func($data1[$key], $data2[$key], $newParent),
                );

                return $acc;
            }

            if ($isFirstExists && $isSecondExists && $data1[$key] !== $data2[$key]) {
                $value1 = $isFirstObject ? $func($data1[$key], $data1[$key], $newParent) : $data1[$key];
                $value2 = $isSecondObject ? $func($data2[$key], $data2[$key], $newParent) : $data2[$key];

                $acc[] = createChangedNode(
                    getTypeIfObject($isFirstObject),
                    getTypeIfObject($isSecondObject),
                    $parent,
                    $key,
                    $value1,
                    $value2,
                );

                return $acc;
            }

            if ($isFirstExists && $isSecondExists) {
                $value = $isFirstObject ? $func($data1[$key], $data1[$key], $newParent) : $data1[$key];
                $acc[] = createNode(getTypeIfObject($isFirstObject), OPERATION_NOT_CHANGED, $parent, $key, $value);

                return $acc;
            }

            if ($isFirstExists) {
                $value = $isFirstObject ? $func($data1[$key], $data1[$key], $newParent) : $data1[$key];
                $acc[] = createNode(getTypeIfObject($isFirstObject), OPERATION_REMOVED, $parent, $key, $value);

                return $acc;
            }

            $value = $isSecondObject ? $func($data2[$key], $data2[$key], $newParent) : $data2[$key];
            $acc[] = createNode(getTypeIfObject($isSecondObject), OPERATION_ADDED, $parent, $key, $value);

            return $acc;
        }, []);
    };

    return $func($data1, $data2, ROOT_PATH);
}
