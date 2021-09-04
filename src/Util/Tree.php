<?php

namespace Util\Tree;

use InvalidArgumentException;

const TYPE_OBJECT = 'object';
const TYPE_SIMPLE = 'simple';
const OPERATION_NOT_CHANGED = 'not_changed';
const OPERATION_CHANGED = 'changed';
const OPERATION_ADDED = 'added';
const OPERATION_REMOVED = 'removed';
const VALUE_OLD = 'old';
const VALUE_NEW = 'new';
const VALUE_CURRENT = 'current';
const ROOT_PATH = '/';

function createNode(string $type, string $operation, string $parent, mixed $key, mixed $value): array
{
    return [
        'operation' => $operation,
        'type'      => $type,
        'parent'    => $parent,
        'key'       => $key,
        'value'     => $value,
    ];
}

function createChangedNode(
    string $oldType,
    string $newType,
    string $parent,
    mixed $key,
    mixed $oldValue,
    mixed $newValue,
): array {
    return [
        'operation' => OPERATION_CHANGED,
        'oldType'   => $oldType,
        'newType'   => $newType,
        'parent'    => $parent,
        'key'       => $key,
        'oldValue'  => $oldValue,
        'newValue'  => $newValue,
    ];
}

function getOperation(array $node): string
{
    return $node['operation'];
}

function getType(array $node): string
{
    if (isChanged($node)) {
        throw new InvalidArgumentException("This node not supported method 'getType'");
    }

    return $node['type'];
}

function getOldType(array $node): string
{
    if (!isChanged($node)) {
        throw new InvalidArgumentException("This node not supported method 'getOldType'");
    }

    return $node['oldType'];
}

function getNewType(array $node): string
{
    if (!isChanged($node)) {
        throw new InvalidArgumentException("This node not supported method 'getNewType'");
    }

    return $node['newType'];
}

function getParent(array $node): string
{
    return $node['parent'];
}

function getKey(array $node): mixed
{
    return $node['key'];
}

function getValue(array $node): mixed
{
    if (isChanged($node)) {
        throw new InvalidArgumentException("This node not supported method 'getValue'");
    }

    return $node['value'];
}

function getOldValue(array $node): mixed
{
    if (!isChanged($node)) {
        throw new InvalidArgumentException("This node not supported method 'getOldValue'");
    }

    return $node['oldValue'];
}

function getNewValue(array $node): mixed
{
    if (!isChanged($node)) {
        throw new InvalidArgumentException("This node not supported method 'getNewValue'");
    }

    return $node['newValue'];
}

function isSimple(array $node): bool
{
    return getType($node) === TYPE_SIMPLE;
}

function isOldSimple(array $node): bool
{
    return getOldType($node) === TYPE_SIMPLE;
}

function isNewSimple(array $node): bool
{
    return getNewType($node) === TYPE_SIMPLE;
}

function isNotChanged(array $node): bool
{
    return getOperation($node) === OPERATION_NOT_CHANGED;
}

function isChanged(array $node): bool
{
    return getOperation($node) === OPERATION_CHANGED;
}

function isAdded(array $node): bool
{
    return getOperation($node) === OPERATION_ADDED;
}

function isRemoved(array $node): bool
{
    return getOperation($node) === OPERATION_REMOVED;
}

function getTypeIfObject(bool $object): string
{
    return $object ? TYPE_OBJECT : TYPE_SIMPLE;
}
