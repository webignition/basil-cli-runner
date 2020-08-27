<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model\ProcessRunResult;

class ProcessRunResult implements ProcessRunResultInterface
{
    private int $exitCode;
    private string $output;
    private string $errorOutput;

    public function __construct(int $exitCode, string $output, string $errorOutput)
    {
        $this->exitCode = $exitCode;
        $this->output = trim($output);
        $this->errorOutput = trim($errorOutput);
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function getErrorOutput(): string
    {
        return $this->errorOutput;
    }

    public function isSuccessful(): bool
    {
        return 0 === $this->exitCode;
    }
}
