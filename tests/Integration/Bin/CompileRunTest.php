<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Integration\Bin;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use webignition\BasilCliRunner\Tests\Model\PhpUnitOutput;
use webignition\BasilCliRunner\Tests\Services\ConsoleStyler;

class CompileRunTest extends TestCase
{
    /**
     * @dataProvider generateAndRunDataProvider
     *
     * @param string $source
     */
    public function testGenerateAndRun(string $source, string $target, string $expectedOutputBody)
    {
        $generateCommand = $this->createGenerateCommand($source, $target);
        shell_exec($generateCommand);

        $runCommand = $this->createRunCommand($target);

        $runCommandOutput = (string) shell_exec($runCommand);
        $phpUnitOutput = new PhpUnitOutput($runCommandOutput);

        $this->assertSame($expectedOutputBody, $phpUnitOutput->getBody());

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
        $styler = new ConsoleStyler();

        return [
            'passing: single test' => [
                'source' => './tests/Fixtures/basil-integration/Test/index-page-test.yml',
                'target' => './tests/build/target',
                'expectedOutputBody' =>
                    $styler->bold('tests/Fixtures/basil-integration/Test/index-page-test.yml') . "\n" .
                    '  ' . $styler->success('✓') . ' ' . $styler->success('verify page is open') . "\n" .
                    '    ' . $styler->success('✓') . ' $page.url is "http://127.0.0.1:9080/index.html"' . "\n" .
                    '    ' . $styler->success('✓') .
                    ' $page.title is "Test fixture web server default document"' . "\n" .
                    '    ' . $styler->success('✓') . ' $page.title matches "/fixture web server/"' . "\n" .
                    "\n"
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

    private function createRunCommand(string $path): string
    {
        return './bin/runner --path=' . $path;
    }
}
