<?php

namespace Differ\Formatters\Json;

use const JSON_PRETTY_PRINT;

function render(array $ast): string
{
    return json_encode($ast, JSON_PRETTY_PRINT);
}
