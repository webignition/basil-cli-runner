<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilCliRunner\Model\ResultPrinter\Comment;
use webignition\BasilCliRunner\Model\ResultPrinter\RenderableInterface;
use webignition\DomElementIdentifier\AttributeIdentifierInterface;
use webignition\DomElementIdentifier\ElementIdentifierInterface;

class ComponentIdentifiedBy implements RenderableInterface
{
    private string $type;
    private Comment $identifier;

    public function __construct(ElementIdentifierInterface $identifier)
    {
        $this->type = $identifier instanceof AttributeIdentifierInterface ? 'attribute' : 'element';
        $this->identifier = new Comment((string) $identifier);
    }

    public function render(): string
    {
        return sprintf(
            '%s %s identified by:',
            $this->type,
            $this->identifier->render()
        );
    }
}
