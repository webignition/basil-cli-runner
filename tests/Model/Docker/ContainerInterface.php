<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model\Docker;

use webignition\BasilCliRunner\Tests\Model\ProcessRunResult\ProcessRunResultInterface;

interface ContainerInterface
{
    public function getName(): string;
    public function getImage(): string;
    public function setup(): ProcessRunResultInterface;
    public function create(): ProcessRunResultInterface;
    public function start(): ProcessRunResultInterface;
    public function remove(): ProcessRunResultInterface;
}
