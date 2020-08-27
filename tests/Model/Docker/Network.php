<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model\Docker;

use Symfony\Component\Process\Process;
use webignition\BasilCliRunner\Tests\Model\AbstractProcessRunner;
use webignition\BasilCliRunner\Tests\Model\ProcessRunResult\CreateNetworkProcessRunResult;
use webignition\BasilCliRunner\Tests\Model\ProcessRunResult\ProcessRunResultInterface;

class Network extends AbstractProcessRunner
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function create(): ProcessRunResultInterface
    {
        $process = Process::fromShellCommandline('docker network create ' . $this->name);
        $exitCode = $process->run();

        return new CreateNetworkProcessRunResult(
            $this->name,
            $exitCode,
            $process->getOutput(),
            $process->getErrorOutput()
        );
    }

    public function connect(ContainerInterface $container): ProcessRunResultInterface
    {
        return self::runProcess('docker network connect ' . $this->name . ' ' . $container->getName());
    }

    public function remove(): ProcessRunResultInterface
    {
        return self::runProcess('docker network rm ' . $this->name);
    }
}
