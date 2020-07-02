<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Services;

use webignition\BasilCliRunner\Services\RunProcessFactory;
use webignition\BasilCliRunner\Tests\Services\ProjectRootPathProvider;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;
use webignition\BasilPhpUnitResultPrinter\ResultPrinter;

class RunProcessFactoryTest extends AbstractBaseTest
{
    private RunProcessFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new RunProcessFactory(
            (new ProjectRootPathProvider())->get()
        );
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(string $path, ?string $printer, string $expectedCommand)
    {
        $process = $this->factory->create($path, $printer);

        self::assertSame($expectedCommand, $process->getCommandLine());
    }

    public function createDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();
        $path = 'path/to/target';

        return [
            'no printer' => [
                'path' => 'path/to/target',
                'printer' => null,
                'expectedCommand' => sprintf(
                    '%s/vendor/bin/phpunit -c %s/phpunit.run.xml --colors=always %s',
                    $root,
                    $root,
                    $path
                ),
            ],
            'has printer' => [
                'path' => 'path/to/target',
                'printer' => 'PrinterClass',
                'expectedCommand' => sprintf(
                    '%s/vendor/bin/phpunit -c %s/phpunit.run.xml --colors=always --printer="%s" %s',
                    $root,
                    $root,
                    ResultPrinter::class,
                    $path
                ),
            ],

        ];
    }
}
