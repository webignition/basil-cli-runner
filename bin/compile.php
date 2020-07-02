<?php

declare(strict_types=1);

$root = (string) realpath(__DIR__ . '/..');

require $root . '/vendor/autoload.php';

use webignition\SingleCommandApplicationPharBuilder\Builder;

$builder = new Builder(
    $root,
    'build/runner.phar',
    'bin/runner',
    [
        'src',
        'vendor/composer',
        'vendor/myclabs',
        'vendor/php-webdriver',
        'vendor/phpunit/phpunit',
        'vendor/symfony',
        'vendor/webignition',
    ]
);

$builder->build();
