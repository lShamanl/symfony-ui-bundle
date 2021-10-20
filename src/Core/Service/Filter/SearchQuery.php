<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Service\Filter;

use Bundle\UIBundle\Core\Dto\Filters;
use Bundle\UIBundle\Core\Dto\Sorts;

/**
 * UIBundleQuery
 */
class SearchQuery
{
    private Pagination $pagination;
    private Filters $filters;
    private Sorts $sorts;

    public function __construct(
        Pagination $pagination,
        Filters $filters,
        Sorts $sorts
    ) {
        $this->pagination = $pagination;
        $this->filters = $filters;
        $this->sorts = $sorts;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    public function getFilters(): Filters
    {
        return $this->filters;
    }

    public function getSorts(): Sorts
    {
        return $this->sorts;
    }

    public function addFilter(Filter $filter): void
    {
        $this->filters->add($filter);
    }

    public function addSort(Sort $sort): void
    {
        $this->sorts->add($sort);
    }

    public function setPagination(Pagination $pagination): void
    {
        $this->pagination = $pagination;
    }
}
