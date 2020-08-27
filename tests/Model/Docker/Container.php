<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model\Docker;

use Symfony\Component\Process\Process;
use webignition\BasilCliRunner\Tests\Model\AbstractProcessRunner;
use webignition\BasilCliRunner\Tests\Model\ProcessRunResult\ProcessRunResultInterface;
use webignition\BasilCliRunner\Tests\Model\ProcessRunResult\RemoveContainerProcessRunResult;

class Container extends AbstractProcessRunner implements ContainerInterface
{
    private string $name;
    private string $image;

    /**
     * @var string[]
     */
    private array $createOptions;

    /**
     * @param string $name
     * @param string $image
     * @param string[] $createOptions
     */
    public function __construct(string $name, string $image, array $createOptions = [])
    {
        $this->name = $name;
        $this->image = $image;
        $this->createOptions = $createOptions;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setup(): ProcessRunResultInterface
    {
        $removeResult = $this->remove();
        if (false === $removeResult->isSuccessful()) {
            return $removeResult;
        }

        $createResult = $this->create();
        if (false === $createResult->isSuccessful()) {
            return $createResult;
        }

        return $this->start();
    }

    public function create(): ProcessRunResultInterface
    {
        $command = sprintf(
            'docker create %s --name %s %s',
            implode(' ', $this->createOptions),
            $this->name,
            $this->image
        );

        return $this->runProcess($command);
    }

    public function start(): ProcessRunResultInterface
    {
        return $this->runProcess('docker start ' . $this->name);
    }

    public function remove(): ProcessRunResultInterface
    {
        $process = Process::fromShellCommandline('docker rm -f ' . $this->name);
        $exitCode = $process->run();

        return new RemoveContainerProcessRunResult(
            $this->name,
            $exitCode,
            $process->getOutput(),
            $process->getErrorOutput()
        );
    }
}
