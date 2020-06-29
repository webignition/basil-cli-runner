<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Model\ResultPrinter\DataSet;

use webignition\BasilCliRunner\Model\ResultPrinter\DataSet\KeyValue;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;

class KeyValueTest extends AbstractBaseTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(KeyValue $keyValue, string $expectedRenderedString)
    {
        $this->assertSame($expectedRenderedString, $keyValue->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'keyValue' => new KeyValue('key', 'value'),
                'expectedRenderedString' => '$key: <comment>value</comment>',
            ],
        ];
    }
}
