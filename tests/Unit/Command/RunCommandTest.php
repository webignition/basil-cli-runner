<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Command;

use phpmock\mockery\PHPMockery;
use Symfony\Component\Console\Tester\CommandTester;
use webignition\BasilCliRunner\Command\RunCommand;
use webignition\BasilCliRunner\Services\CommandFactory;
use webignition\BasilCliRunner\Services\ProjectRootPathProvider;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;

class RunCommandTest extends AbstractBaseTest
{
    public function testRunUnableToStartBackgroundProcess()
    {
        $input = [
            '--path' => (new ProjectRootPathProvider())->get() . '/tests',
        ];

        $command = CommandFactory::createRunCommand();

        $commandTester = new CommandTester($command);

        PHPMockery::mock('webignition\BasilCliRunner\Command', 'popen')->andReturn(false);

        $exitCode = $commandTester->execute($input);
        $this->assertSame(RunCommand::RETURN_CODE_UNABLE_TO_OPEN_PROCESS, $exitCode);
    }
}
