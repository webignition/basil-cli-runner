<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter\StatementLine;

use webignition\BasilCliRunner\Model\ResultPrinter\HighlightedFailure;
use webignition\BasilCliRunner\Model\ResultPrinter\Literal;
use webignition\BasilCliRunner\Model\ResultPrinter\RenderableInterface;
use webignition\BasilCliRunner\Model\ResultPrinter\StatusIcon;
use webignition\BasilCliRunner\Model\TestOutput\Status;
use webignition\BasilModels\StatementInterface;

class Header implements RenderableInterface
{
    private StatusIcon $statusIcon;
    private RenderableInterface $source;

    public function __construct(StatementInterface $statement, int $status)
    {
        $this->statusIcon = new StatusIcon($status);
        $this->source = Status::SUCCESS === $status
            ? new Literal($statement->getSource())
            : new HighlightedFailure($statement->getSource());
    }

    public function render(): string
    {
        return sprintf(
            '%s %s',
            $this->statusIcon->render(),
            $this->source->render()
        );
    }
}
