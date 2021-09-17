<?php

namespace Formatters\Stylish;

use function array_reduce;
use function implode;
use function str_repeat;
use function Util\Tree\getKey;
use function Util\Tree\getNewValue;
use function Util\Tree\getOldValue;
use function Util\Tree\getOperation;
use function Util\Tree\getValue;
use function Util\Tree\isChanged;
use function Util\Tree\isNewSimple;
use function Util\Tree\isOldSimple;
use function Util\Tree\isSimple;

use const Util\Tree\OPERATION_ADDED;
use const Util\Tree\OPERATION_REMOVED;
use const Util\Tree\VALUE_CURRENT;
use const Util\Tree\VALUE_NEW;
use const Util\Tree\VALUE_OLD;

function format(array $ast): string
{
    $str = formatAst($ast);

    return "{\n{$str}\n}";
}

function formatAst(array $ast, int $depth = 0): string
{
    $lines = array_reduce($ast, function (array $acc, $node) use ($depth): array {
        if (isChanged($node)) {
            $space1 = getLeftSpace($node, $depth, OPERATION_REMOVED);
            $space2 = getLeftSpace($node, $depth, OPERATION_ADDED);
            $value1 = getItemValue($node, $depth, VALUE_OLD);
            $value2 = getItemValue($node, $depth, VALUE_NEW);

            return [...$acc, "{$space1}{$value1}", "{$space2}{$value2}"];
        }

        $space = getLeftSpace($node, $depth, getOperation($node));
        $value = getItemValue($node, $depth, VALUE_CURRENT);

        return [...$acc, "{$space}{$value}"];
    }, []);

    return implode("\n", $lines);
}

function getLeftSpace($node, int $depth, string $operation): string
{
    $spaceLength = getSpaceLength($depth);
    $space = str_repeat(' ', $spaceLength - 2);
    $symbol = match ($operation) {
        OPERATION_ADDED => '+',
        OPERATION_REMOVED => '-',
        default => ' ',
    };
    $key = getKey($node);

    return "{$space}{$symbol} {$key}: ";
}

function getItemValue($node, int $depth, string $type): string
{
    $valueGetter = match ($type) {
        VALUE_OLD => fn($node) => getOldValue($node),
        VALUE_NEW => fn($node) => getNewValue($node),
        default => fn($node) => getValue($node),
    };

    $simpleGetter = match ($type) {
        VALUE_OLD => fn($node) => isOldSimple($node),
        VALUE_NEW => fn($node) => isNewSimple($node),
        default => fn($node) => isSimple($node),
    };

    if ($simpleGetter($node)) {
        return toString($valueGetter($node));
    }

    $str = formatAst($valueGetter($node), $depth + 1);
    $space = str_repeat(' ', getSpaceLength($depth));

    return "{\n{$str}\n{$space}}";
}

function getSpaceLength(int $depth): int
{
    return ($depth + 1) * 4;
}

function toString(mixed $value): string
{
    return match (gettype($value)) {
        'boolean' => $value ? 'true' : 'false',
        'NULL' => 'null',
        default => (string)$value,
    };
}
