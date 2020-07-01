<?php

declare(strict_types=1);

namespace webignition\BasilRunner\Tests\Functional\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use webignition\BasilCliRunner\Command\RunCommand;
use webignition\BasilCliRunner\Services\CommandFactory;
use webignition\BasilCliRunner\Tests\Services\ProjectRootPathProvider;

class RunCommandTest extends TestCase
{
    private RunCommand $command;

    protected function setUp(): void
    {
        parent::setUp();

        $root = (new ProjectRootPathProvider())->get();
        $this->command = CommandFactory::createRunCommand($root);
    }

    public function testRunFailurePathDoesNotExist()
    {
        $input = [
            '--path' => __DIR__ . '/non-existent',
        ];

        $output = new BufferedOutput();

        $exitCode = $this->command->run(new ArrayInput($input), $output);
        $this->assertSame(RunCommand::RETURN_CODE_INVALID_PATH, $exitCode);

        $commandOutputContent = $output->fetch();
        $this->assertEquals('', $commandOutputContent);
    }
}
