<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilCliRunner\Model\ResultPrinter\Comment;
use webignition\BasilCliRunner\Model\ResultPrinter\RenderableInterface;

class ScalarToScalarComparisonSummary implements RenderableInterface
{
    private ComparisonOperator $comparisonOperator;
    private Comment $expectedValue;
    private Comment $actualValue;

    public function __construct(string $operator, string $expectedValue, string $actualValue)
    {
        $this->comparisonOperator = new ComparisonOperator($operator);
        $this->expectedValue = new Comment($expectedValue);
        $this->actualValue = new Comment($actualValue);
    }

    public function render(): string
    {
        return sprintf(
            "* %s %s %s",
            $this->actualValue->render(),
            $this->comparisonOperator->render(),
            $this->expectedValue->render()
        );
    }
}
