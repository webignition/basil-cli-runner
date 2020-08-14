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
        $suiteManifest = $this->createSuiteManifest($source, $target);

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
            'passing: multiple tests' => [
                'source' => $root . '/tests/Fixtures/basil-integration/Test',
                'target' => $root . '/tests/build/target',
            ],
        ];
    }

    /**
     * @dataProvider commandOutputIsStreamedDataProvider
     *
     * @param string $source
     * @param string $target
     * @param string[] $expectedBufferPatterns
     */
    public function testCommandOutputIsStreamed(string $source, string $target, array $expectedBufferPatterns)
    {
        $suiteManifest = $this->createSuiteManifest($source, $target);

        $testManifest = $suiteManifest->getTestManifests()[0];
        $testPath = $testManifest->getTarget();

        $runCommand = $this->createRunCommand($testPath);
        $runProcess = Process::fromShellCommandline($runCommand);

        $now = microtime(true);

        $bufferCount = 0;
        $exitCode = $runProcess->run(function ($type, $buffer) use ($expectedBufferPatterns, &$bufferCount, &$now) {
            self::assertMatchesRegularExpression($expectedBufferPatterns[$bufferCount], $buffer);
            self::assertGreaterThanOrEqual(0.009, microtime(true) - $now);

            $now = microtime(true);
            $bufferCount++;
        });

        self::assertSame(0, $exitCode);

        unlink($testPath);
    }

    public function commandOutputIsStreamedDataProvider(): array
    {
        $root = (string) getcwd();
        $testRelativePath = 'tests/Fixtures/basil-integration/Test/follow-links-test.yml';

        return [
            'default' => [
                'source' => $root . '/' . $testRelativePath,
                'target' => $root . '/tests/build/target',
                'expectedBufferPatterns' => [
                    '/^---\n' .
                    'type: test\n' .
                    'path: ' . preg_quote($testRelativePath, '/') . '\n' .
                    '\.\.\.\n' .
                    '$/m',
                    '/^---\n' .
                    'type: step\n' .
                    'name: \'verify page is open\'\n' .
                    '/m',
                    '/^---\n' .
                    'type: step\n' .
                    'name: \'follow link to form page\'\n' .
                    '/m',
                    '/^---\n' .
                    'type: step\n' .
                    'name: \'follow link to index page\'\n' .
                    '/m',
                ],
            ],
        ];
    }

    private function createSuiteManifest(string $source, string $target): SuiteManifest
    {
        $generateCommand = $this->createGenerateCommand($source, $target);

        $generateOutput = [];
        $generateExitCode = null;
        exec($generateCommand, $generateOutput, $generateExitCode);
        self::assertSame(0, $generateExitCode);

        $generateOutputData = Yaml::parse(implode("\n", $generateOutput));

        return SuiteManifest::fromArray($generateOutputData);
    }

    private function createGenerateCommand(string $source, string $target): string
    {
        return 'php ./compiler.phar ' .
            '--source=' . $source . ' ' .
            '--target=' . $target . ' ' .
            '--base-class="' . AbstractGeneratedTestCase::class . '"';
    }
}
