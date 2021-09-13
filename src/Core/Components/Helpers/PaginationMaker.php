<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Components\Helpers;

use Bundle\UIBundle\Core\Service\Filter\Pagination;
use Symfony\Component\HttpFoundation\Request;

class PaginationMaker
{
    public static function make(Request $request): Pagination
    {
        $paginationParam = $request->query->get('page', []);

        $hasPaginationParam = !empty($paginationParam) && is_array($paginationParam);
        $hasPaginationData = isset($paginationParam['number']) && isset($paginationParam['size']);

        if (!$hasPaginationParam || !$hasPaginationData) {
            return new Pagination(1, 20);
        } else {
            return new Pagination(
                (int) $paginationParam['number'],
                (int) $paginationParam['size']
            );
        }
    }
}
