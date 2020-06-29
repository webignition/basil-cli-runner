<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Services\ResultPrinter\ModelFactory;

use webignition\BasilCliRunner\Model\ResultPrinter\Exception\InvalidLocator;
use webignition\BasilCliRunner\Model\ResultPrinter\Exception\Unknown;
use webignition\BasilCliRunner\Model\ResultPrinter\RenderableInterface;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidLocatorException;

class ExceptionFactory
{
    public function create(\Throwable $exception): RenderableInterface
    {
        if ($exception instanceof InvalidLocatorException) {
            return new InvalidLocator($exception);
        }

        return new Unknown($exception);
    }
}
