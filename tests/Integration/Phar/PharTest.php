<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Integration\Phar;

use webignition\BasilCliRunner\Tests\Integration\AbstractCompileRunTest;
use webignition\BasilCliRunner\Tests\Services\ProjectRootPathProvider;

class PharTest extends AbstractCompileRunTest
{
    protected function setUp(): void
    {
        parent::setUp();

        self::assertFileExists((new ProjectRootPathProvider())->get() . '/runner.phar');
    }

    protected function createRunCommand(string $path): string
    {
        return 'php ./runner.phar --path=' . $path;
    }
}
