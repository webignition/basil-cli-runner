<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model\ProcessRunResult;

class RemoveContainerProcessRunResult extends ProcessRunResult
{
    private string $name;

    public function __construct(string $name, int $exitCode, string $output, string $errorOutput)
    {
        parent::__construct($exitCode, $output, $errorOutput);

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSuccessful(): bool
    {
        if (parent::isSuccessful()) {
            return true;
        }

        return 'Error: No such container: ' . $this->getName() === $this->getErrorOutput();
    }
}
