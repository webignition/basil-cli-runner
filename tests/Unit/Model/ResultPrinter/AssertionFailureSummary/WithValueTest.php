<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilCliRunner\Model\ResultPrinter\AssertionFailureSummary\WithValue;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;

class WithValueTest extends AbstractBaseTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(WithValue $withValue, string $expectedRenderedString)
    {
        $this->assertSame($expectedRenderedString, $withValue->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'withValue' => new WithValue('no indent'),
                'expectedRenderedString' => 'with value <comment>no indent</comment>',
            ],
        ];
    }
}
