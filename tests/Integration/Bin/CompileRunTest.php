<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Integration\Bin;

use webignition\BasilCliRunner\Tests\Integration\AbstractCompileRunTest;
use webignition\BasilPhpUnitResultPrinter\ResultPrinter;

class CompileRunTest extends AbstractCompileRunTest
{
    protected function createRunCommand(string $path): string
    {
        return './bin/runner --path=' . $path . ' --printer="' . ResultPrinter::class . '"';
    }
}
