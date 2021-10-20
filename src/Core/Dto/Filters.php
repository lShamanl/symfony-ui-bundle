<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Dto;

use Bundle\UIBundle\Core\Service\Filter\Filter;
use TypeError;

/**
 * UIBundleQuery
 */
class Filters
{
    /** @var Filter[] */
    protected array $filters;

    /**
     * Filters constructor.
     * @param Filter[] $filters
     */
    public function __construct(array $filters = [])
    {
        foreach ($filters as $filter) {
            if (!$filter instanceof Filter) {
                throw new TypeError('Variable is not ' . Filter::class);
            }
        }
        $this->filters = $filters;
    }

    public function add(Filter $filter): void
    {
        $this->filters[] = $filter;
    }

    /**
     * @return Filter[]
     */
    public function toArray(): array
    {
        return $this->filters;
    }
}
