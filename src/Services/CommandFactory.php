<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Services;

use webignition\BasilCliRunner\Command\RunCommand;

class CommandFactory
{
    public static function createRunCommand(string $projectRootPath): RunCommand
    {
        return new RunCommand($projectRootPath, new RunProcessFactory($projectRootPath));
    }
}
