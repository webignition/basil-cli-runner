<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model\ProcessRunResult;

class CreateNetworkProcessRunResult extends ProcessRunResult
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

        $failedSuccessfullyMessage =
            'Error response from daemon: network with name ' . $this->getName() . ' already exists';

        return $failedSuccessfullyMessage === $this->getErrorOutput();
    }
}
