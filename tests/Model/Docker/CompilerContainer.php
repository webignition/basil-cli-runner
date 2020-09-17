<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model\Docker;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Yaml\Yaml;
use webignition\BasilCompilerModels\SuiteManifest;
use webignition\TcpCliProxyClient\Client;
use webignition\TcpCliProxyModels\Output;

class CompilerContainer extends Container
{
    private const SOURCE_PATH = '/app/basil/%s';
    private const TARGET_PATH = '/app/generated/%s';
    public function __construct(string $name, string $image, BrowserRunners $browserRunners)
    {
        parent::__construct(
            $name,
            $image,
            $this->createCreateOptions($browserRunners)
        );
    }

    public function compileForBrowserRunner(BrowserRunner $browserRunner): SuiteManifest
    {
        $browser = $browserRunner->getBrowserName();

        $output = new BufferedOutput();
        $compilerClient = Client::createFromHostAndPort('localhost', $this->getLocalPort());
        $compilerClient = $compilerClient->withOutput($output);

        $compilerClient->request(sprintf(
            './compiler --source=%s --target=%s',
            $this->getSourcePathForBrowser($browser),
            $this->getTargetPathForBrowser($browser)
        ));

        $output = Output::fromString($output->fetch());
        if (0 !== $output->getExitCode()) {
            throw new \RuntimeException($output->getContent(), $output->getExitCode());
        }

        $manifestData = Yaml::parse($output->getContent());

        return SuiteManifest::fromArray($manifestData);
    }

    /**
     * @param BrowserRunners $browserRunners
     *
     * @return string[]
     */
    private function createCreateOptions(BrowserRunners $browserRunners): array
    {
        $options = [
            '-p 8000',
        ];

        foreach ($browserRunners->get() as $browserRunner) {
            $browserName = $browserRunner->getBrowserName();
            $containerSourcePath = $this->getSourcePathForBrowser($browserName);
            $containerTargetPath = $this->getTargetPathForBrowser($browserName);

            $options[] = '-v ' . $browserRunner->getLocalSourcePath() . ':' . $containerSourcePath;
            $options[] = '-v ' . $browserRunner->getLocalTargetPath() . ':' . $containerTargetPath;
        }

        return $options;
    }

    private function getSourcePathForBrowser(string $browser): string
    {
        return sprintf(self::SOURCE_PATH, $browser);
    }

    private function getTargetPathForBrowser(string $browser): string
    {
        return sprintf(self::TARGET_PATH, $browser);
    }
}
