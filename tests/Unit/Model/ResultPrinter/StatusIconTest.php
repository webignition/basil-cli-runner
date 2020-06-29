<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Model\ResultPrinter;

use webignition\BasilCliRunner\Model\ResultPrinter\StatusIcon;
use webignition\BasilCliRunner\Model\TestOutput\Status;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;

class StatusIconTest extends AbstractBaseTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(StatusIcon $statusIcon, string $expectedRenderedString)
    {
        $this->assertSame($expectedRenderedString, $statusIcon->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'success' => [
                'statusIcon' => new StatusIcon(Status::SUCCESS),
                'expectedRenderedString' => '<icon-success />',
            ],
            'failure' => [
                'statusIcon' => new StatusIcon(Status::FAILURE),
                'expectedRenderedString' => '<icon-failure />',
            ],
            'unknown' => [
                'statusIcon' => new StatusIcon(-1),
                'expectedRenderedString' => '<icon-unknown />',
            ],
        ];
    }
}
