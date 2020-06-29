<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Model\ResultPrinter;

use webignition\BasilCliRunner\Model\ResultPrinter\Literal;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;

class LiteralTest extends AbstractBaseTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(Literal $literal, string $expectedRenderedString)
    {
        $this->assertSame($expectedRenderedString, $literal->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'comment' => new Literal('content'),
                'expectedRenderedString' => 'content',
            ],
        ];
    }
}
