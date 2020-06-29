<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\TestOutput;

use PHPUnit\Runner\BaseTestRunner;

class Status
{
    public const SUCCESS = BaseTestRunner::STATUS_PASSED;
    public const FAILURE = BaseTestRunner::STATUS_FAILURE;
}
