<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Service\Filter;

/**
 * UIBundleQuery
 */
class Filter
{
    private const DEFAULT_MODE = FilterSqlBuilder::EQUALS;

    private string $property;
    private mixed $value;
    private string $searchMode;

    public function __construct(
        string $property,
        mixed $value,
        string $mode = self::DEFAULT_MODE
    ) {

        $this->property = $property;
        $this->value = $value;
        $this->searchMode = $mode;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getSearchMode(): string
    {
        return $this->searchMode;
    }

    public function setPropertyName(string $property): void
    {
        $this->property = $property;
    }
}
