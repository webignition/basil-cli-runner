<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Services;

use Symfony\Component\Process\Process;
use webignition\BasilPhpUnitResultPrinter\ResultPrinter;

class RunProcessFactory
{
    private string $projectRootPath;

    public function __construct(string $projectRootPath)
    {
        $this->projectRootPath = $projectRootPath;
    }

    public function create(string $path, ?string $printer): Process
    {
        return Process::fromShellCommandline($this->createPhpUnitCommand($path, $printer));
    }

    private function createPhpUnitCommand(string $path, ?string $printer): string
    {
        $phpUnitExecutablePath = $this->projectRootPath . '/vendor/bin/phpunit';
        $phpUnitConfigurationPath = $this->projectRootPath . '/phpunit.run.xml';

        $command = $phpUnitExecutablePath .
            ' -c ' . $phpUnitConfigurationPath .
            ' --colors=always';

        if (null !== $printer) {
            $command .= ' --printer="' . ResultPrinter::class . '"';
        }

        $command .= ' ' . $path;

        return $command;
    }
}
