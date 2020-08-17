<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use webignition\BasilCliRunner\Services\BufferHandler;
use webignition\BasilCliRunner\Services\RunProcessFactory;
use webignition\SymfonyConsole\TypedInput\TypedInput;

class RunCommand extends Command
{
    public const OPTION_PATH = 'path';

    public const RETURN_CODE_INVALID_PATH = 100;
    public const RETURN_CODE_UNABLE_TO_RUN_PROCESS = 200;

    private const NAME = 'run';

    private RunProcessFactory $runProcessFactory;

    public function __construct(RunProcessFactory $runProcessFactory)
    {
        $this->runProcessFactory = $runProcessFactory;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Command description')
            ->addOption(
                self::OPTION_PATH,
                null,
                InputOption::VALUE_REQUIRED,
                'Absolute path to a test to run.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $typedInput = new TypedInput($input);

        $path = trim((string) $typedInput->getStringOption(RunCommand::OPTION_PATH));
        if (!is_file($path)) {
            return self::RETURN_CODE_INVALID_PATH;
        }

        $process = $this->runProcessFactory->create($path);
        $bufferHandler = new BufferHandler();

        try {
            $process->mustRun(function ($type, $buffer) use ($output, $bufferHandler) {
                if (Process::OUT === $type) {
                    $handledBuffer = $bufferHandler->handle($buffer);

                    if (null !== $handledBuffer) {
                        $output->write($buffer);
                    }
                }
            });
        } catch (ProcessFailedException $processFailedException) {
            return self::RETURN_CODE_UNABLE_TO_RUN_PROCESS;
        }

        return 0;
    }
}
