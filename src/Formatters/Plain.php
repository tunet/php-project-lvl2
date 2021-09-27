<?php

namespace Formatters\Plain;

use function Tree\getKey;
use function Tree\getNewValue;
use function Tree\getOldValue;
use function Tree\getValue;
use function Tree\isAdded;
use function Tree\isChanged;
use function Tree\isNotChanged;
use function Tree\isObject;

function format(array $ast): string
{
    $func = function (array $ast, array $acc = [], array $path = []) use (&$func): array {
        return array_reduce($ast, function (array $acc, $node) use ($func, $path): array {
            $currentPath = [...$path, getKey($node)];
            $property = implode('.', $currentPath);

            if (isNotChanged($node) && isObject($node)) {
                return $func(getValue($node), $acc, $currentPath);
            }

            if (isNotChanged($node)) {
                return $acc;
            }

            if (isChanged($node)) {
                $oldValue = toString(getOldValue($node));
                $newValue = toString(getNewValue($node));

                return [...$acc, "Property '{$property}' was updated. From {$oldValue} to {$newValue}"];
            }

            if (isAdded($node)) {
                $value = toString(getValue($node));

                return [...$acc, "Property '{$property}' was added with value: {$value}"];
            }

            return [...$acc, "Property '{$property}' was removed"];
        }, $acc);
    };

    $result = $func($ast);

    return implode("\n", $result);
}

function toString(mixed $value): string
{
    return match (gettype($value)) {
        'boolean' => true === $value ? 'true' : 'false', // bool expression -> fix phpstan
        'NULL' => 'null',
        'string' => "'{$value}'",
        'array' => '[complex value]',
        default => (string)$value,
    };
}
