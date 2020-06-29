<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter;

use webignition\BasilCliRunner\Model\TestOutput\Status;

class StatusIcon implements RenderableInterface
{
    public const SUCCESS = '<icon-success />';
    public const FAILURE = '<icon-failure />';
    public const UNKNOWN = '<icon-unknown />';

    private int $status;

    public function __construct(int $status)
    {
        $this->status = $status;
    }

    public function render(): string
    {
        if (Status::SUCCESS === $this->status) {
            return self::SUCCESS;
        }

        if (Status::FAILURE === $this->status) {
            return self::FAILURE;
        }

        return self::UNKNOWN;
    }
}
