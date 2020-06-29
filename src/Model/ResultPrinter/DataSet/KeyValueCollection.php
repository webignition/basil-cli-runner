<?php

declare(strict_types=1);

namespace webignition\BasilCliRunner\Model\ResultPrinter\DataSet;

use webignition\BasilCliRunner\Model\ResultPrinter\RenderableInterface;
use webignition\BasilModels\DataSet\DataSetInterface;

class KeyValueCollection implements RenderableInterface
{
    /**
     * @var KeyValue[]
     */
    private array $keyValues;

    /**
     * @param KeyValue[] $keyValues
     */
    public function __construct(array $keyValues)
    {
        $this->keyValues = array_filter($keyValues, function ($item) {
            return $item instanceof KeyValue;
        });
    }

    public static function fromDataSet(DataSetInterface $dataSet): self
    {
        $keyValues = [];

        foreach ($dataSet->getData() as $key => $value) {
            $keyValues[] = new KeyValue((string) $key, $value);
        }

        return new KeyValueCollection($keyValues);
    }

    public function render(): string
    {
        $renderedKeyValues = [];

        foreach ($this->keyValues as $keyValue) {
            $renderedKeyValues[] = $keyValue->render();
        }

        return implode("\n", $renderedKeyValues);
    }
}
