<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilCliRunner\Model\ResultPrinter\AssertionFailureSummary\WithParent;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;

class WithParentTest extends AbstractBaseTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(WithParent $withParent, string $expectedRenderedString)
    {
        $this->assertSame($expectedRenderedString, $withParent->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'withParent' => new WithParent(),
                'expectedRenderedString' => 'with parent:',
            ],
        ];
    }
}
