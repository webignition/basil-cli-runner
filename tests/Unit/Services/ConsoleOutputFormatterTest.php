<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Tests\Unit\Services;

use webignition\BasilCliRunner\Model\ResultPrinter\Failure;
use webignition\BasilCliRunner\Model\ResultPrinter\HighlightedFailure;
use webignition\BasilCliRunner\Model\ResultPrinter\StatusIcon;
use webignition\BasilCliRunner\Model\ResultPrinter\Success;
use webignition\BasilCliRunner\Model\ResultPrinter\TestName;
use webignition\BasilCliRunner\Model\TestOutput\Status;
use webignition\BasilCliRunner\Services\ConsoleOutputFormatter;
use webignition\BasilCliRunner\Tests\Unit\AbstractBaseTest;

class ConsoleOutputFormatterTest extends AbstractBaseTest
{
    private ConsoleOutputFormatter $formatter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatter = new ConsoleOutputFormatter();
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testFormat(string $line, string $expectedFormattedString)
    {
        $this->assertSame($expectedFormattedString, $this->formatter->format($line));
    }

    public function formatDataProvider(): array
    {
        return [
            'test name' => [
                'line' => (new TestName('test.yml'))->render(),
                'expectedFormattedString' => '<options=bold>test.yml</>',
            ],
            'success' => [
                'line' => (new Success('content'))->render(),
                'expectedFormattedString' => '<fg=green>content</>',
            ],
            'failure' => [
                'line' => (new Failure('content'))->render(),
                'expectedFormattedString' => '<fg=red>content</>',
            ],
            'highlighted failure' => [
                'line' => (new HighlightedFailure('content'))->render(),
                'expectedFormattedString' => '<fg=white;bg=red>content</>',
            ],
            'status icon: success' => [
                'line' => (new StatusIcon(Status::SUCCESS))->render(),
                'expectedFormattedString' => '<fg=green>✓</>',
            ],
            'status icon: failure' => [
                'line' => (new StatusIcon(Status::FAILURE))->render(),
                'expectedFormattedString' => '<fg=red>x</>',
            ],
        ];
    }
}
