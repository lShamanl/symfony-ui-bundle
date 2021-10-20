<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Contract\Filter;

use Bundle\UIBundle\Core\Contract\Filter\Traits\FilterContractTrait;
use Bundle\UIBundle\Core\Contract\Filter\Traits\PaginationContractTrait;
use Bundle\UIBundle\Core\Contract\Filter\Traits\SortContractTrait;

/**
 * UIBundleQuery
 */
class FilterSortPagination
{
    use FilterContractTrait;
    use SortContractTrait;
    use PaginationContractTrait;
}
