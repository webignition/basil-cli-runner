<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter\Step;

use webignition\BaseBasilTestCase\BasilTestCaseInterface;
use webignition\BasilCliRunner\Model\ResultPrinter\Failure;
use webignition\BasilCliRunner\Model\ResultPrinter\RenderableInterface;
use webignition\BasilCliRunner\Model\ResultPrinter\StatusIcon;
use webignition\BasilCliRunner\Model\ResultPrinter\Success;
use webignition\BasilCliRunner\Model\TestOutput\Status;
use webignition\BasilModels\DataSet\DataSetInterface;

class Name implements RenderableInterface
{
    private StatusIcon $statusIcon;
    private RenderableInterface $nameLine;

    public function __construct(BasilTestCaseInterface $test)
    {
        $status = $test->getStatus();
        $name = $test->getBasilStepName();
        $dataSet = $test->getCurrentDataSet();
        if ($dataSet instanceof DataSetInterface) {
            $name .= ': ' . $dataSet->getName();
        }

        $this->statusIcon = new StatusIcon($status);
        $this->nameLine = Status::SUCCESS === $status
            ? new Success($name)
            : new Failure($name);
    }

    public function render(): string
    {
        return sprintf(
            '%s %s',
            $this->statusIcon->render(),
            $this->nameLine->render()
        );
    }
}
