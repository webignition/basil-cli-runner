<?php

declare(strict_types=1);

$root = (string) realpath(__DIR__ . '/..');

require $root . '/vendor/autoload.php';

use webignition\BasilCliRunner\Services\PharBuilder;

$binPath = __DIR__ . '/runner';

$pharBuilder = new PharBuilder();
$pharBuilder->build($root, $binPath);
