<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

abstract class AbstractCompileRunTest extends TestCase
{
    abstract protected function createRunCommand(string $path): string;

    /**
     * @dataProvider generateAndRunDataProvider
     *
     * @param string $source
     */
    public function testGenerateAndRun(string $source, string $target)
    {
        $generateCommand = $this->createGenerateCommand($source, $target);
        shell_exec($generateCommand);

        $runCommand = $this->createRunCommand($target);

        $commandOutput = [];
        $exitCode = null;
        exec($runCommand, $commandOutput, $exitCode);

        self::assertSame(0, $exitCode);

        $finder = new Finder();
        $finder
            ->files()
            ->name('*.php')
            ->in($target);

        foreach ($finder as $file) {
            $filename = (string) realpath((string) $file);

            if (file_exists($filename)) {
                unlink($filename);
            }
        }
    }

    public function generateAndRunDataProvider(): array
    {
        return [
            'passing: single test' => [
                'source' => './tests/Fixtures/basil-integration/Test/index-page-test.yml',
                'target' => './tests/build/target',
            ],
        ];
    }

    private function createGenerateCommand(string $source, string $target): string
    {
        return 'php ./compiler.phar ' .
            '--source=' . $source . ' ' .
            '--target=' . $target . ' ' .
            '--base-class="' . AbstractGeneratedTestCase::class . '"';
    }
}
