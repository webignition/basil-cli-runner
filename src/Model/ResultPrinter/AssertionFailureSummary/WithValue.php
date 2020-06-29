<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilCliRunner\Model\ResultPrinter\Comment;
use webignition\BasilCliRunner\Model\ResultPrinter\RenderableInterface;

class WithValue implements RenderableInterface
{
    private Comment $value;

    public function __construct(string $value)
    {
        $this->value = new Comment($value);
    }

    public function render(): string
    {
        return sprintf(
            'with value %s',
            $this->value->render()
        );
    }
}
