<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model\Docker;

class BrowserRunner
{
    private const SOURCE_PATH = '%s/build/test/basil/%s';
    private const TARGET_PATH = '%s/build/test/generated/%s';

    private string $imageName;

    public function __construct(string $imageName)
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): string
    {
        return $this->imageName;
    }

    public function getBrowserName(): string
    {
        $repoNameTagParts = explode(':', $this->imageName);
        $repoName = $repoNameTagParts[0] ?? '';
        $repoNameParts = explode('/', $repoName);
        $browserNamePart = $repoNameParts[1] ?? '';

        return str_replace('-runner', '', $browserNamePart);
    }

    public function getLocalSourcePath(): string
    {
        return sprintf(self::SOURCE_PATH, getcwd(), $this->getBrowserName());
    }

    public function getLocalTargetPath(): string
    {
        return sprintf(self::TARGET_PATH, getcwd(), $this->getBrowserName());
    }
}
