<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Model\Docker;

use webignition\BasilCliRunner\Tests\Model\AbstractProcessRunner;

class BrowserRunners extends AbstractProcessRunner implements \Countable
{
    /**
     * @var BrowserRunner[]
     */
    private array $runners;

    public function __construct(string $tag)
    {
        var_dump($tag);
        var_dump($this->runProcess('docker images')->getOutput());
        var_dump($this->runProcess('docker images | tail -n +2')->getOutput());
        var_dump($this->runProcess('docker images | tail -n +2 | awk \'{print $1":"$2}\'')->getOutput());
        var_dump($this->runProcess(
            'docker images | tail -n +2 | awk \'{print $1":"$2}\' | grep "smartassert/.*-runner:"'
        )->getOutput());
        var_dump($this->runProcess(
            'docker images | tail -n +2 | awk \'{print $1":"$2}\' | grep "smartassert/.*-runner:' . $tag . '"'
        )->getOutput());

//        exit();

        $processResult = $this->runProcess(
            'docker images | tail -n +2 | awk \'{print $1":"$2}\' | grep "smartassert/.*-runner:' . $tag . '"'
        );

        if (false === $processResult->isSuccessful()) {
            var_dump($processResult->getExitCode(), $processResult->getOutput(), $processResult->getErrorOutput());

            throw new \RuntimeException($processResult->getErrorOutput(), $processResult->getExitCode());
        }

        $imageNames = explode("\n", $processResult->getOutput());
        $runners = [];

        foreach ($imageNames as $imageName) {
            $runners[] = new BrowserRunner($imageName);
        }

        $this->runners = $runners;
    }

    /**
     * @return BrowserRunner[]
     */
    public function get(): array
    {
        return $this->runners;
    }

    /**
     * @return string[]
     */
    public function getNames(): array
    {
        $names = [];

        foreach ($this->runners as $browserRunner) {
            $names[] = $browserRunner->getBrowserName();
        }

        sort($names);

        return $names;
    }

    public function count(): int
    {
        return count($this->runners);
    }
}
