<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Services\ResultPrinter\ModelFactory;

use Facebook\WebDriver\Exception\InvalidSelectorException;
use webignition\BasilCliRunner\Model\ResultPrinter\Exception\InvalidLocator;
use webignition\BasilCliRunner\Model\ResultPrinter\Exception\Unknown;
use webignition\BasilCliRunner\Model\ResultPrinter\RenderableInterface;
use webignition\BasilCliRunner\Services\ResultPrinter\ModelFactory\ExceptionFactory;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;
use webignition\DomElementIdentifier\ElementIdentifier;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidLocatorException;

class ExceptionFactoryTest extends AbstractBaseTest
{
    private ExceptionFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ExceptionFactory();
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(\Throwable $exception, RenderableInterface $expectedModel)
    {
        $this->assertEquals($expectedModel, $this->factory->create($exception));
    }

    public function createDataProvider(): array
    {
        $invalidLocatorException = new InvalidLocatorException(
            new ElementIdentifier('a[href=https://example.com]'),
            \Mockery::mock(InvalidSelectorException::class)
        );

        $logicException = new \LogicException('logic exception message');

        return [
            'InvalidLocatorException: CSS selector' => [
                'exception' => $invalidLocatorException,
                'expectedModel' => new InvalidLocator($invalidLocatorException),
            ],
            'unknown exception' => [
                'exception' => $logicException,
                'expectedModel' => new Unknown($logicException),
            ],
        ];
    }
}
