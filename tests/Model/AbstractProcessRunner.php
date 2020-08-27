<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model;

use Symfony\Component\Process\Process;
use webignition\BasilCliRunner\Tests\Model\ProcessRunResult\ProcessRunResult;
use webignition\BasilCliRunner\Tests\Model\ProcessRunResult\ProcessRunResultInterface;

abstract class AbstractProcessRunner
{
    protected function runProcess(string $command): ProcessRunResultInterface
    {
        $process = Process::fromShellCommandline($command);
        $exitCode = $process->run();

        return new ProcessRunResult(
            $exitCode,
            $process->getOutput(),
            $process->getErrorOutput()
        );
    }
}
