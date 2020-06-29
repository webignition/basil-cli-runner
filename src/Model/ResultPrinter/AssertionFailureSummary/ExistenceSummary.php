<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilCliRunner\Model\ResultPrinter\IndentedContent;
use webignition\BasilCliRunner\Model\ResultPrinter\Literal;
use webignition\BasilCliRunner\Model\ResultPrinter\RenderableCollection;
use webignition\DomElementIdentifier\ElementIdentifierInterface;

class ExistenceSummary extends RenderableCollection
{
    public function __construct(ElementIdentifierInterface $identifier, string $operator)
    {
        $ancestorHierarchy = null === $identifier->getParentIdentifier()
            ? null
            : new IndentedContent(new AncestorHierarchy($identifier));

        parent::__construct([
            new ComponentIdentifiedBy($identifier),
            new IndentedContent(new IdentifierProperties($identifier), 2),
            $ancestorHierarchy,
            new IndentedContent(new Literal('exists' === $operator ? 'does not exist' : 'does exist'))
        ]);
    }

    public function render(): string
    {
        $content = parent::render();

        $content = ucfirst($content);
        return '* ' . $content;
    }
}
