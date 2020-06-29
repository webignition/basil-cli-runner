<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter;

interface RenderableInterface
{
    public function render(): string;
}
