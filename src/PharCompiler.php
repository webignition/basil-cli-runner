<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner;

use Phar;
use Symfony\Component\Finder\Finder;

class PharCompiler
{
    public const DEFAULT_PHAR_FILENAME = 'build/runner.phar';

    private string $baseDirectory;

    public function __construct()
    {
        $this->baseDirectory = (string) realpath(__DIR__ . '/..');
    }

    public function compile(string $pharFile = self::DEFAULT_PHAR_FILENAME): void
    {
        $phar = new Phar($pharFile, 0, 'runner.phar');
        $phar->startBuffering();

        $this->addBinRunner($phar);

        $filesIterator = $this->createFilesFinder([
            'src',
            'vendor/composer',
            'vendor/myclabs',
            'vendor/php-webdriver',
            'vendor/phpunit/phpunit',
            'vendor/symfony',
            'vendor/webignition',
        ]);

        $phar->buildFromIterator($filesIterator, $this->baseDirectory);

        $this->addVendorAutoload($phar);

        $phar->setStub($this->createStub());
        $phar->stopBuffering();
    }

    private function addBinRunner(Phar $phar): void
    {
        $content = (string) file_get_contents(__DIR__ . '/../bin/runner');
        $content = (string) preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/runner', $content);
    }

    /**
     * @param string[] $paths
     *
     * @return \Iterator<\SplFileInfo>
     */
    private function createFilesFinder(array $paths): \Iterator
    {
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->exclude('Tests')
            ->exclude('tests')
            ->exclude('docs')
            ->in($paths);

        return $finder->getIterator();
    }

    private function addVendorAutoload(Phar $phar): void
    {
        $phar->addFile('vendor/autoload.php');
    }

    private function createStub(): string
    {
        return <<< EOF
#!/usr/bin/env php
<?php

Phar::mapPhar('runner.phar');

require 'phar://runner.phar/bin/runner';

__HALT_COMPILER();

EOF;
    }
}
