<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilCliRunner\Model\ResultPrinter\IndentedContent;
use webignition\BasilCliRunner\Model\ResultPrinter\Literal;
use webignition\BasilCliRunner\Model\ResultPrinter\RenderableCollection;
use webignition\DomElementIdentifier\ElementIdentifierInterface;

class ElementalIsRegExpSummary extends RenderableCollection
{
    public function __construct(ElementIdentifierInterface $identifier, string $regexp)
    {
        $ancestorHierarchy = null === $identifier->getParentIdentifier()
            ? null
            : new IndentedContent(new AncestorHierarchy($identifier));

        parent::__construct([
            new ComponentIdentifiedBy($identifier),
            new IndentedContent(new IdentifierProperties($identifier), 2),
            $ancestorHierarchy,
            new IndentedContent(new Literal('is not a valid regular expression')),
            new Literal(''),
            new ScalarIsRegExpSummary($regexp)
        ]);
    }

    public function render(): string
    {
        $content = parent::render();
        return '* The value of ' . $content;
    }
}
