<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Services;

use webignition\BasilCliRunner\Model\ResultPrinter\Failure;
use webignition\BasilCliRunner\Model\ResultPrinter\HighlightedFailure;
use webignition\BasilCliRunner\Model\ResultPrinter\StatusIcon;
use webignition\BasilCliRunner\Model\ResultPrinter\Success;
use webignition\BasilCliRunner\Model\ResultPrinter\TestName;
use webignition\BasilCliRunner\Model\TestOutput\IconMap;
use webignition\BasilCliRunner\Model\TestOutput\Status;

class ConsoleOutputFormatter
{
    public function format(string $line): string
    {
        if ($this->isTestNameLine($line)) {
            return $this->formatTestNameLine($line);
        }

        $line = str_replace(
            StatusIcon::SUCCESS,
            '<success>' . IconMap::get(Status::SUCCESS) . '</success>',
            $line
        );

        $line = str_replace(
            StatusIcon::FAILURE,
            '<failure>' . IconMap::get(Status::FAILURE) . '</failure>',
            $line
        );

        $line = str_replace(Success::START, '<fg=green>', $line);
        $line = str_replace(Success::END, '</>', $line);

        $line = str_replace(Failure::START, '<fg=red>', $line);
        $line = str_replace(Failure::END, '</>', $line);

        $line = str_replace(HighlightedFailure::START, '<fg=white;bg=red>', $line);

        return str_replace(HighlightedFailure::END, '</>', $line);
    }

    private function isTestNameLine(string $line): bool
    {
        $pattern = '/^' . preg_quote(TestName::START, '/') . '.*' . preg_quote(TestName::END, '/') . '$/';

        return preg_match($pattern, $line) > 0;
    }

    private function formatTestNameLine(string $line): string
    {
        return (string) preg_replace(
            [
                '/^' . preg_quote(TestName::START, '/') . '/',
                '/' . preg_quote(TestName::END, '/') . '$/',
            ],
            [
                '<options=bold>',
                '</>',
            ],
            $line
        );
    }
}
