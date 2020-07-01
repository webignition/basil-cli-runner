<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use webignition\BasilCliRunner\PharCompiler;

$pharCompiler = new PharCompiler();
$pharCompiler->compile();
