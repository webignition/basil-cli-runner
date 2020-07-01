<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Services;

use webignition\BasilCliRunner\Command\RunCommand;

class CommandFactory
{
    public static function createRunCommand(): RunCommand
    {
        $projectRootPath = (new ProjectRootPathProvider())->get();

        return new RunCommand(
            $projectRootPath,
            new ConsoleOutputFormatter(),
            new RunProcessFactory($projectRootPath)
        );
    }
}
