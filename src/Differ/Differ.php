<?php

namespace Differ\Differ;

use function array_key_exists;
use function array_keys;
use function array_reduce;
use function array_unique;
use function file_get_contents;
use function gettype;
use function Parsers\Parsers\getParser;
use function sort;

function genDiff(string $filePath1, string $filePath2): string
{
    $data1 = getParsedData($filePath1);
    $data2 = getParsedData($filePath2);

    $keys = array_merge(array_keys($data1), array_keys($data2));
    $keys = array_unique($keys);
    sort($keys);

    $result = array_reduce($keys, function (array $acc, $key) use ($data1, $data2) {
        if (array_key_exists($key, $data1) && !array_key_exists($key, $data2)) {
            $value = toString($data1[$key]);
            $acc[] = "  - {$key}: {$value}";

            return $acc;
        }

        if (!array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
            $value = toString($data2[$key]);
            $acc[] = "  + {$key}: {$value}";

            return $acc;
        }

        if ($data1[$key] === $data2[$key]) {
            $value = toString($data1[$key]);
            $acc[] = "    {$key}: {$value}";

            return $acc;
        }

        $value1 = toString($data1[$key]);
        $acc[] = "  - {$key}: {$value1}";

        $value2 = toString($data2[$key]);
        $acc[] = "  + {$key}: {$value2}";

        return $acc;
    }, []);

    $strResult = implode("\n", $result);

    return "{\n{$strResult}\n}";
}

function getParsedData(string $filePath): array
{
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $parser = getParser($extension);
    $content = file_get_contents($filePath);

    return $parser($content);
}

function toString($value): string
{
    return match (gettype($value)) {
        'boolean' => $value ? 'true' : 'false',
        'NULL' => 'null',
        default => (string) $value,
    };
}
