<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilCliRunner\Model\ResultPrinter\AssertionFailureSummary\AncestorHierarchy;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;
use webignition\DomElementIdentifier\ElementIdentifier;

class AncestorHierarchyTest extends AbstractBaseTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(AncestorHierarchy $ancestorHierarchy, string $expectedRenderedString)
    {
        $this->assertSame($expectedRenderedString, $ancestorHierarchy->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'parent > child' => [
                'ancestorHierarchy' => new AncestorHierarchy(
                    (new ElementIdentifier('.child'))
                        ->withParentIdentifier(new ElementIdentifier('.parent'))
                ),
                'expectedRenderedString' =>
                    'with parent:' . "\n" .
                    '  - CSS selector: <comment>.parent</comment>' . "\n" .
                    '  - ordinal position: <comment>1</comment>',
            ],
            'grandparent > parent > child' => [
                'ancestorHierarchy' => new AncestorHierarchy(
                    (new ElementIdentifier('.child'))
                        ->withParentIdentifier(
                            (new ElementIdentifier('.parent'))
                            ->withParentIdentifier(
                                new ElementIdentifier('.grandparent')
                            )
                        )
                ),
                'expectedRenderedString' =>
                    'with parent:' . "\n" .
                    '  - CSS selector: <comment>.parent</comment>' . "\n" .
                    '  - ordinal position: <comment>1</comment>' . "\n" .
                    'with parent:' . "\n" .
                    '  - CSS selector: <comment>.grandparent</comment>' . "\n" .
                    '  - ordinal position: <comment>1</comment>',
            ],
        ];
    }
}
