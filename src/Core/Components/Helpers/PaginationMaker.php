<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Components\Helpers;

use Bundle\UIBundle\Core\Service\Filter\Pagination;
use Symfony\Component\HttpFoundation\Request;

/**
 * UIBundleQuery
 */
class PaginationMaker
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_SIZE = 20;

    public static function make(Request $request): Pagination
    {
        /** @var mixed $paginationRaw */
        $paginationRaw = $request->query->get('page');
        if (!isset($paginationRaw)) {
            return self::createDefaultPagination();
        }
        if (!is_array($paginationRaw)) {
            return self::createDefaultPagination();
        }
        #todo: Добавить дефолтные вещи в ENV-файл, чтобы их можно было переопределять: query ?? env ?? default
        return new Pagination(
            (int) ($paginationRaw['number'] ?? self::DEFAULT_PAGE),
            (int) ($paginationRaw['size'] ?? self::DEFAULT_SIZE)
        );
    }

    protected static function createDefaultPagination(): Pagination
    {
        return new Pagination(self::DEFAULT_PAGE, self::DEFAULT_SIZE);
    }
}
