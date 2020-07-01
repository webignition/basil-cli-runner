<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\TestOutput;

class Test
{
    private string $testPath;

    public function __construct(string $testPath)
    {
        $this->testPath = $testPath;
    }

    public function hasPath(string $path): bool
    {
        return $this->testPath === $path;
    }

    public function getPath(): string
    {
        return $this->testPath;
    }
}
