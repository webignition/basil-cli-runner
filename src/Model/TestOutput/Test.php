<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\TestOutput;

class Test
{
    private string $testPath;
    private string $projectRootPath;

    public function __construct(string $testPath, string $projectRootPath)
    {
        $this->testPath = $testPath;
        $this->projectRootPath = $projectRootPath;
    }

    public function hasPath(string $path): bool
    {
        return $this->testPath === $path;
    }

    public function getRelativePath(): string
    {
        return substr($this->testPath, strlen($this->projectRootPath) + 1);
    }
}
