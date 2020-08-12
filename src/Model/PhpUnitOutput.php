<?php

namespace webignition\BasilCliRunner\Model;

class PhpUnitOutput
{
    private const YAML_DOCUMENT_SEPARATOR = '---';

    private string $body;

    public function __construct(string $content)
    {
        $contentLines = explode(self::YAML_DOCUMENT_SEPARATOR, $content, 2);
        $this->body = self::YAML_DOCUMENT_SEPARATOR . $contentLines[1];
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
