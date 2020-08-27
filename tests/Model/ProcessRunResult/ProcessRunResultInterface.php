<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model\ProcessRunResult;

interface ProcessRunResultInterface
{
    public function getExitCode(): int;
    public function getOutput(): string;
    public function getErrorOutput(): string;
    public function isSuccessful(): bool;
}
