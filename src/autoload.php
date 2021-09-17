<?php

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1; // @phpstan-ignore-line
} else {
    require_once $autoloadPath2; // @phpstan-ignore-line
}
