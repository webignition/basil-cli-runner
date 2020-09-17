<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use webignition\BasilCliRunner\Tests\Model\Docker\BrowserContainer;
use webignition\BasilCliRunner\Tests\Model\Docker\BrowserRunner;
use webignition\BasilCliRunner\Tests\Model\Docker\BrowserRunners;
use webignition\BasilCliRunner\Tests\Model\Docker\CompilerContainer;
use webignition\BasilCliRunner\Tests\Model\Docker\Container;
use webignition\BasilCliRunner\Tests\Model\Docker\ContainerInterface;
use webignition\BasilCliRunner\Tests\Model\Docker\Network;
use webignition\BasilCliRunner\Tests\Model\ProcessRunResult\ProcessRunResultInterface;
use webignition\TcpCliProxyClient\Client;
use webignition\TcpCliProxyModels\Output;

class DockerImageTest extends TestCase
{
    private const COMPILER_IMAGE = 'smartassert/basil-compiler:0.23';
    private const NGINX_IMAGE = 'nginx:1.19';

    private const COMPILER_LOCAL_PORT = 9002;
    private const RUNNER_LOCAL_PORT = 9003;
    private const RUNNER_PATH = '/app/generated';

    private const COMPILER_CONTAINER_NAME = 'test-compiler-container';
    private const NGINX_CONTAINER_NAME = 'test-nginx-container';
    private const NETWORK_NAME = 'test-network';

    private const EXPECTED_RUNNER_BROWSER_NAMES = [
        'chrome',
        'firefox',
    ];

    private const EXPECTED_BROWSER_RUNNER_COUNT = 2;
    private const EXPECTED_STEP_PASSED_COUNT = 4;
    private const EXPECTED_STATEMENT_PASSED_COUNT = 12;

    private static CompilerContainer $compilerContainer;
    private static ContainerInterface $nginxContainer;
    private static Network $network;

    /**
     * @var BrowserRunners
     */
    private static BrowserRunners $browserRunners;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$browserRunners = new BrowserRunners(self::getImageTag());
        self::assertCount(self::EXPECTED_BROWSER_RUNNER_COUNT, self::$browserRunners);
        self::assertSame(self::EXPECTED_RUNNER_BROWSER_NAMES, self::$browserRunners->getNames());

        self::createCompilerContainer();
        self::createNginxContainer();
        self::createNetwork();

        self::$network->connect(self::$nginxContainer);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::$compilerContainer->remove();
        self::$nginxContainer->remove();
        self::$network->remove();
    }

    /**
     * @dataProvider runnerDataProvider
     */
    public function testRunner(BrowserRunner $browserRunner)
    {
        $browser = $browserRunner->getBrowserName();
        $browserRunnerContainerName = 'test-' . $browser . '-container';

        $suiteManifest = self::$compilerContainer->compileForBrowserRunner($browserRunner);
        $testManifest = $suiteManifest->getTestManifests()[0];
        $browserRunnerContainer = self::createBrowserRunnerContainer($browserRunnerContainerName, $browserRunner);
        self::$network->connect($browserRunnerContainer);

        $browserRunnerClient = Client::createFromHostAndPort('localhost', self::RUNNER_LOCAL_PORT);

        $browserRunnerClientOutput = new BufferedOutput();
        $browserRunnerClient = $browserRunnerClient->withOutput($browserRunnerClientOutput);

        $browserRunnerClient->request(sprintf(
            './bin/runner --path=%s/%s',
            self::RUNNER_PATH,
            basename($testManifest->getTarget())
        ));

        $outputContent = $browserRunnerClientOutput->fetch();
        $outputObject = Output::fromString($outputContent);
        self::assertSame(0, $outputObject->getExitCode());
        self::assertRunnerOutput($outputObject);

        $removeBrowserContainerResult = $browserRunnerContainer->remove();
        self::assertProcessResult($removeBrowserContainerResult);

        $generatedFilesToDelete = glob($browserRunner->getLocalTargetPath() . '/*.php');
        if (is_array($generatedFilesToDelete)) {
            foreach ($generatedFilesToDelete as $generatedFile) {
                unlink($generatedFile);
            }
        }
    }

    public function runnerDataProvider(): array
    {
        $browserRunners = new BrowserRunners(self::getImageTag());
        self::assertCount(self::EXPECTED_BROWSER_RUNNER_COUNT, $browserRunners);

        $dataSets = [];

        foreach ($browserRunners->get() as $browserRunner) {
            $data = [
                'browserRunner' => $browserRunner,
            ];

            $dataSets[$browserRunner->getImageName()] = $data;
        }

        return $dataSets;
    }

    private static function assertProcessResult(ProcessRunResultInterface $result): void
    {
        self::assertTrue($result->isSuccessful(), $result->getErrorOutput());
    }

    private static function getImageTag(): string
    {
        return $_ENV['TRAVIS_BRANCH'] ?? 'master';
    }

    private static function createCompilerContainer(): void
    {
        self::$compilerContainer = new CompilerContainer(
            self::COMPILER_CONTAINER_NAME,
            self::COMPILER_IMAGE,
            self::COMPILER_LOCAL_PORT,
            self::$browserRunners
        );

        $setupResult = self::$compilerContainer->setup();
        self::assertProcessResult($setupResult);
    }

    private static function createNginxContainer(): void
    {
        self::$nginxContainer = new Container(
            self::NGINX_CONTAINER_NAME,
            self::NGINX_IMAGE,
            [
                '-v ' . getcwd() . '/build/test/html:/usr/share/nginx/html',
            ]
        );

        $setupResult = self::$nginxContainer->setup();
        self::assertProcessResult($setupResult);
    }

    private static function createNetwork(): void
    {
        self::$network = new Network(self::NETWORK_NAME);
        $createResult = self::$network->create();
        self::assertProcessResult($createResult);
    }

    private static function createBrowserRunnerContainer(string $name, BrowserRunner $browserRunner): BrowserContainer
    {
        $browserContainer = new BrowserContainer(
            $name,
            $browserRunner->getImageName(),
            self::RUNNER_LOCAL_PORT,
            $browserRunner->getLocalTargetPath(),
            self::RUNNER_PATH
        );

        $setupResult = $browserContainer->setup();
        self::assertProcessResult($setupResult);

        return $browserContainer;
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
