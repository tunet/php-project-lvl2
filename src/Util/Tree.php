<?php

namespace Util\Tree;

const TYPE_OBJECT = 'object';
const TYPE_SIMPLE = 'simple';
const OPERATION_NOT_CHANGED = 'not_changed';
const OPERATION_ADDED = 'added';
const OPERATION_REMOVED = 'removed';

function createNode(string $type, string $operation, mixed $key, mixed $value): array
{
    return [
        'operation' => $operation,
        'type'      => $type,
        'key'       => $key,
        'value'     => $value,
    ];
}

function getValue(array $node): mixed
{
    return $node['value'];
}

function isObject(array $node): bool
{
    return TYPE_OBJECT === $node['type'];
}

function isSimple(array $node): bool
{
    return TYPE_SIMPLE === $node['type'];
}

function isNotChanged(array $node): bool
{
    return OPERATION_NOT_CHANGED === $node['operation'];
}

function isAdded(array $node): bool
{
    return OPERATION_ADDED === $node['operation'];
}

function isRemoved(array $node): bool
{
    return OPERATION_REMOVED === $node['operation'];
}

function getTypeIfObject(bool $object): string
{
    return $object ? TYPE_OBJECT : TYPE_SIMPLE;
}
