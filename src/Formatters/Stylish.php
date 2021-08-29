<?php

namespace Formatters\Stylish;

use function array_reduce;
use function implode;
use function str_repeat;

function format(array $ast): string
{
    $str = formatAst($ast);

    return "{\n{$str}\n}";
}

function formatAst(array $ast, int $depth = 0): string {
    $spaceLength = getSpaceLength($depth);

    $lines = array_reduce($ast, function (array $acc, array $node) use ($depth, $spaceLength): array {
        if ('changed' === $node['operation']) {
            $space1 = str_repeat(' ', $spaceLength - 2) . "- ";
            $acc[] = "{$space1}{$node['key']}: " . getItemValue($node['oldType'], $node['oldValue'], $depth);

            $space2 = str_repeat(' ', $spaceLength - 2) . "+ ";
            $acc[] = "{$space2}{$node['key']}: " . getItemValue($node['newType'], $node['newValue'], $depth);

            return $acc;
        }

        if ('not_changed' === $node['operation']) {
            $space = str_repeat(' ', $spaceLength);
        } else {
            $symbol = 'added' === $node['operation'] ? '+' : '-';
            $space = str_repeat(' ', $spaceLength - 2) . "{$symbol} ";
        }

        $acc[] = "{$space}{$node['key']}: " . getItemValue($node['type'], $node['value'], $depth);

        return $acc;
    }, []);

    return implode("\n", $lines);
}

function getItemValue(string $type, mixed $value, int $depth): string
{
    if ('simple' === $type) {
        return toString($value);
    }

    $str = formatAst($value, $depth + 1);
    $space = str_repeat(' ', getSpaceLength($depth));

    return "{\n{$str}\n{$space}}";
}

function getSpaceLength(int $depth): int
{
    return ($depth + 1) * 4;
}

function toString($value): string
{
    return match (gettype($value)) {
        'boolean' => $value ? 'true' : 'false',
        'NULL' => 'null',
        default => (string) $value,
    };
}
