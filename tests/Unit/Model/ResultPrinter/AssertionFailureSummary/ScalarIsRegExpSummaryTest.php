<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilCliRunner\Model\ResultPrinter\AssertionFailureSummary\ScalarIsRegExpSummary;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;

class ScalarIsRegExpSummaryTest extends AbstractBaseTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ScalarIsRegExpSummary $scalarIsRegExpSummary, string $expectedRenderedString)
    {
        $this->assertSame($expectedRenderedString, $scalarIsRegExpSummary->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'scalarIsRegExpSummary' => new ScalarIsRegExpSummary('/invalid/'),
                'expectedRenderedString' => '* <comment>/invalid/</comment> is not a valid regular expression',
            ],
        ];
    }
}
