<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Yaml\Yaml;
use webignition\BasilCompilerModels\SuiteManifest;
use webignition\TcpCliProxyClient\Client;
use webignition\TcpCliProxyModels\Output;

class DockerImageTest extends TestCase
{
    private const COMPILER_SOURCE_PATH = '/app/source';
    private const COMPILER_TARGET_PATH = '/app/target';
    private const COMPILER_PORT = 9500;

    private const CHROME_RUNNER_PORT = 9501;
    private const FIREFOX_RUNNER_PORT = 9502;

    private const EXPECTED_STEP_PASSED_COUNT = 4;
    private const EXPECTED_STATEMENT_PASSED_COUNT = 12;

    /**
     * @dataProvider runTestInBrowserRunnerDataProvider
     */
    public function testRunTestInBrowserRunner(string $source, int $runnerPort): void
    {
        $suiteManifest = $this->compileSource($source);
        $testManifests = $suiteManifest->getTestManifests();
        $testManifest = $testManifests[0];
        $testPath = $testManifest->getTarget();

        $browserRunnerClient = Client::createFromHostAndPort('localhost', $runnerPort);

        $browserRunnerClientOutput = new BufferedOutput();
        $browserRunnerClient = $browserRunnerClient->withOutput($browserRunnerClientOutput);

        $browserRunnerClient->request(sprintf(
            './bin/runner --path=%s/%s',
            self::COMPILER_TARGET_PATH,
            basename($testPath)
        ));

        $outputContent = $browserRunnerClientOutput->fetch();

        $outputObject = Output::fromString($outputContent);
        self::assertSame(0, $outputObject->getExitCode());
        self::assertRunnerOutput($outputObject);
    }

    /**
     * @return array<mixed>
     */
    public function runTestInBrowserRunnerDataProvider(): array
    {
        return [
            'chrome' => [
                'source' => self::COMPILER_SOURCE_PATH . '/chrome-open-index.yml',
                'runnerPort' => self::CHROME_RUNNER_PORT,
            ],
            'firefox' => [
                'source' => self::COMPILER_SOURCE_PATH . '/firefox-open-index.yml',
                'runnerPort' => self::FIREFOX_RUNNER_PORT,
            ],
        ];
    }

    private function compileSource(string $source): SuiteManifest
    {
        $output = new BufferedOutput();
        $compilerClient = Client::createFromHostAndPort('localhost', self::COMPILER_PORT);
        $compilerClient = $compilerClient->withOutput($output);

        $compilerClient->request(sprintf(
            './compiler --source=%s --target=%s',
            $source,
            self::COMPILER_TARGET_PATH
        ));

        $output = Output::fromString($output->fetch());
        if (0 !== $output->getExitCode()) {
            throw new \RuntimeException($output->getContent(), $output->getExitCode());
        }

        $manifestData = Yaml::parse($output->getContent());

        return SuiteManifest::fromArray($manifestData);
    }

    private static function assertRunnerOutput(Output $output): void
    {
        self::assertSame(0, $output->getExitCode(), $output->getContent());

        $content = $output->getContent();

        $outputLines = explode("\n", $content);

        $stepPassedCount = 0;
        $statementPassedCount = 0;

        foreach ($outputLines as $outputLine) {
            if (preg_match('/^status: passed/', $outputLine) === 1) {
                $stepPassedCount++;
            }

            if (preg_match('/^ {4}status: passed/', $outputLine) === 1) {
                $statementPassedCount++;
            }
        }

        self::assertSame(self::EXPECTED_STEP_PASSED_COUNT, $stepPassedCount, $content);
        self::assertSame(self::EXPECTED_STATEMENT_PASSED_COUNT, $statementPassedCount, $content);
    }
}
