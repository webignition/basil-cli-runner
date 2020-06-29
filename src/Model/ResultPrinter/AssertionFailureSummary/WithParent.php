<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter\AssertionFailureSummary;

use webignition\BasilCliRunner\Model\ResultPrinter\Literal;

class WithParent extends Literal
{
    public function __construct()
    {
        parent::__construct('with parent:');
    }
}
