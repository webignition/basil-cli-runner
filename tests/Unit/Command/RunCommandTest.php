<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Command;

use phpmock\mockery\PHPMockery;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use webignition\BasilCliRunner\Command\RunCommand;
use webignition\BasilCliRunner\Services\CommandFactory;
use webignition\BasilCliRunner\Services\RunProcessFactory;
use webignition\BasilCliRunner\Tests\Services\ProjectRootPathProvider;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;
use webignition\ObjectReflector\ObjectReflector;

class RunCommandTest extends AbstractBaseTest
{
    /**
     * @dataProvider executeFailureDataProvider
     *
     * @param RunCommand $command
     * @param array<mixed> $input
     * @param int $expectedExitCode
     */
    public function testExecuteFailure(RunCommand $command, array $input, int $expectedExitCode)
    {
        $commandTester = new CommandTester($command);

        PHPMockery::mock(
            'webignition\\BasilCliRunner\\Command',
            'is_file'
        )->andReturnFalse();

        $exitCode = $commandTester->execute($input);
        $this->assertSame($expectedExitCode, $exitCode);
    }

    public function executeFailureDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();
        $command = CommandFactory::createRunCommand($root);

        return [
            'path does not exist' => [
                'command' => $command,
                'input' => [
                    '--path' => $root . '/invalid/NonExistent.php',
                ],
                'expectedExitCode' => RunCommand::RETURN_CODE_INVALID_PATH,
            ],
        ];
    }

    public function testProcessFailedToRun()
    {
        $root = (new ProjectRootPathProvider())->get();
        $path = $root . '/tests/GeneratedTest.php';
        $input = [
            '--path' => $path,
        ];

        PHPMockery::mock(
            'webignition\\BasilCliRunner\\Command',
            'is_file'
        )->andReturnTrue();

        $command = CommandFactory::createRunCommand($root);

        ObjectReflector::setProperty(
            $command,
            RunCommand::class,
            'runProcessFactory',
            $this->createRunProcessFactory($path, $this->createProcess())
        );

        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute($input);
        $this->assertSame(RunCommand::RETURN_CODE_UNABLE_TO_RUN_PROCESS, $exitCode);
    }

    private function createRunProcessFactory(string $path, Process $return): RunProcessFactory
    {
        $factory = \Mockery::mock(RunProcessFactory::class);
        $factory
            ->shouldReceive('create')
            ->with($path)
            ->andReturn($return);

        return $factory;
    }

    private function createProcess(): Process
    {
        $process = \Mockery::mock(Process::class);

        $process
            ->shouldReceive('isSuccessful')
            ->andReturnFalse();

        $process
            ->shouldReceive('getCommandLine')
            ->andReturn('comand line');

        $process
            ->shouldReceive('getExitCode')
            ->andReturn(9);

        $process
            ->shouldReceive('getExitCodeText')
            ->andReturn('exit code text');

        $process
            ->shouldReceive('getWorkingDirectory')
            ->andReturn('working directory');

        $process
            ->shouldReceive('isOutputDisabled')
            ->andReturnFalse();

        $process
            ->shouldReceive('getOutput')
            ->andReturn('');

        $process
            ->shouldReceive('getErrorOutput')
            ->andReturn('');

        $process
            ->shouldReceive('mustRun')
            ->andThrow(new ProcessFailedException($process));

        return $process;
    }
}
