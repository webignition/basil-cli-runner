<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Services\ResultPrinter;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use PHPUnit\Util\Printer;
use webignition\BaseBasilTestCase\BasilTestCaseInterface;
use webignition\BasilCliRunner\Model\ResultPrinter\IndentedContent;
use webignition\BasilCliRunner\Model\ResultPrinter\TestName;
use webignition\BasilCliRunner\Model\TestOutput\Test as TestOutput;
use webignition\BasilCliRunner\Services\ResultPrinter\ModelFactory\StepFactory;

class ResultPrinter extends Printer implements \PHPUnit\TextUI\ResultPrinter
{
    private ?TestOutput $currentTestOutput = null;
    private StepFactory $stepFactory;

    public function __construct($out = null)
    {
        parent::__construct($out);

        $this->stepFactory = StepFactory::createFactory();
    }

    /**
     * @inheritDoc
     */
    public function addError(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addError() method.
    }

    /**
     * @inheritDoc
     */
    public function addWarning(Test $test, Warning $e, float $time): void
    {
        // TODO: Implement addWarning() method.
    }

    /**
     * @inheritDoc
     */
    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
        // TODO: Implement addFailure() method.
    }

    /**
     * @inheritDoc
     */
    public function addIncompleteTest(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addIncompleteTest() method.
    }

    /**
     * @inheritDoc
     */
    public function addRiskyTest(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addRiskyTest() method.
    }

    /**
     * @inheritDoc
     */
    public function addSkippedTest(Test $test, \Throwable $t, float $time): void
    {
        // TODO: Implement addSkippedTest() method.
    }

    /**
     * @param TestSuite<Test> $suite
     */
    public function startTestSuite(TestSuite $suite): void
    {
        // TODO: Implement startTestSuite() method.
    }

    /**
     * @param TestSuite<Test> $suite
     */
    public function endTestSuite(TestSuite $suite): void
    {
        // TODO: Implement endTestSuite() method.
    }

    /**
     * @inheritDoc
     */
    public function startTest(Test $test): void
    {
        if ($test instanceof BasilTestCaseInterface) {
            $testPath = $test::getBasilTestPath();

            $isNewTest = $this->currentTestOutput instanceof TestOutput
                ? false === $this->currentTestOutput->hasPath($testPath)
                : true;

            if ($isNewTest) {
                $currentTestOutput = new TestOutput($testPath);
                $this->write((new TestName($currentTestOutput->getPath()))->render());
                $this->writeEmptyLine();

                $this->currentTestOutput = $currentTestOutput;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function endTest(Test $test, float $time): void
    {
        if ($test instanceof BasilTestCaseInterface) {
            $indentedRenderedStep = new IndentedContent(
                $this->stepFactory->create($test)
            );

            $this->write($indentedRenderedStep->render());
            $this->writeEmptyLine();
            $this->writeEmptyLine();
        }
    }

    private function writeEmptyLine(): void
    {
        $this->write("\n");
    }

    public function printResult(TestResult $result): void
    {
        // @todo: Implement in #361
    }
}
