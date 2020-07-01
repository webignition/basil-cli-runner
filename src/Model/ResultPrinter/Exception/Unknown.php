<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter\Exception;

use webignition\BasilCliRunner\Model\ResultPrinter\RenderableInterface;

class Unknown implements RenderableInterface
{
    private \Throwable $exception;

    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function render(): string
    {
        return sprintf(
            'An unknown exception has occurred:' . "\n" .
            '    - %s' . "\n" .
            '    - %s',
            get_class($this->exception),
            $this->exception->getMessage()
        );
    }
}