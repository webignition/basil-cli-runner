<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Model\ResultPrinter;

use webignition\BasilCliRunner\Model\ResultPrinter\Failure;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;

class FailureTest extends AbstractBaseTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(Failure $failure, string $expectedRenderedString)
    {
        $this->assertSame($expectedRenderedString, $failure->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'failure' => new Failure('content'),
                'expectedRenderedString' => '<failure>content</failure>',
            ],
        ];
    }
}
