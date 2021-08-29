<?php

namespace Formatters\Stylish;

use function array_reduce;
use function implode;
use function str_repeat;
use function Util\Tree\getValue;
use function Util\Tree\isAdded;
use function Util\Tree\isNotChanged;
use function Util\Tree\isSimple;

function format(array $ast): string
{
    $str = formatAst($ast);

    return "{\n{$str}\n}";
}

function formatAst(array $ast, int $depth = 0): string
{
    $spaceLength = getSpaceLength($depth);

    $lines = array_reduce($ast, function (array $acc, array $node) use ($depth, $spaceLength): array {
        if (isNotChanged($node)) {
            $space = str_repeat(' ', $spaceLength);
        } else {
            $symbol = isAdded($node) ? '+' : '-';
            $space = str_repeat(' ', $spaceLength - 2) . "{$symbol} ";
        }

        $acc[] = "{$space}{$node['key']}: " . getItemValue($node, $depth);

        return $acc;
    }, []);

    return implode("\n", $lines);
}

function getItemValue($node, int $depth): string
{
    if (isSimple($node)) {
        return toString(getValue($node));
    }

    $str = formatAst(getValue($node), $depth + 1);
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
        'NULL'    => 'null',
        default   => (string) $value,
    };
}
