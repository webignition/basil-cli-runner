<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use webignition\BasilCompilerModels\SuiteManifest;

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

        $generateOutput = [];
        $generateExitCode = null;
        exec($generateCommand, $generateOutput, $generateExitCode);
        self::assertSame(0, $generateExitCode);

        $generateOutputData = Yaml::parse(implode("\n", $generateOutput));
        $suiteManifest = SuiteManifest::fromArray($generateOutputData);

        foreach ($suiteManifest->getTestManifests() as $testManifest) {
            $testPath = $testManifest->getTarget();
            self::assertFileExists($testPath);

            $runCommand = $this->createRunCommand($testPath);
            $runProcess = Process::fromShellCommandline($runCommand);

            $commandOutput = [];
            $exitCode = $runProcess->run(function ($type, $buffer) use ($commandOutput) {
                if (Process::OUT === $type) {
                    $commandOutput[] = $buffer;
                }
            });

            self::assertSame(0, $exitCode);

            unlink($testPath);
        }
    }

    public function generateAndRunDataProvider(): array
    {
        $root = (string) getcwd();

        return [
            'passing: single test' => [
                'source' => $root . '/tests/Fixtures/basil-integration/Test/index-page-test.yml',
                'target' => $root . '/tests/build/target',
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
