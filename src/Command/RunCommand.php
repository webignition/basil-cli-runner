<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use webignition\BasilCliRunner\Services\RunProcessFactory;
use webignition\SymfonyConsole\TypedInput\TypedInput;

class RunCommand extends Command
{
    public const OPTION_PATH = 'path';

    public const RETURN_CODE_INVALID_PATH = 100;
    public const RETURN_CODE_UNABLE_TO_RUN_PROCESS = 200;

    private const NAME = 'run';
    private const DEFAULT_RELATIVE_PATH = '/generated';

    private string $projectRootPath;
    private RunProcessFactory $runProcessFactory;

    public function __construct(string $projectRootPath, RunProcessFactory $runProcessFactory)
    {
        $this->projectRootPath = $projectRootPath;
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
                'Absolute path to a test to run.',
                $this->projectRootPath . self::DEFAULT_RELATIVE_PATH
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

        $output->setDecorated(true);

        $process = $this->runProcessFactory->create($path);
        $process->run();

        if (false === $process->isSuccessful()) {
            return self::RETURN_CODE_UNABLE_TO_RUN_PROCESS;
        }

        $output->write($process->getOutput());

        return 0;
    }
}
