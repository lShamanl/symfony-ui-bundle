<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Components\Helpers;

use Bundle\UIBundle\Core\Dto\Sorts;
use Bundle\UIBundle\Core\Service\Filter\Sort;
use Symfony\Component\HttpFoundation\Request;

/**
 * UIBundleQuery
 */
class SortsMaker
{
    public static function make(Request $request): Sorts
    {
        /** @var string $sortRaw */
        $sortRaw = $request->query->get('sort', '');
        if (empty($sortRaw)) {
            return new Sorts();
        }

        $sortParams = explode(
            ',',
            str_replace(' ', '', $sortRaw)
        );

        $sorts = [];
        foreach ($sortParams as $sortParam) {
            $field = trim($sortParam, '-');

            $direction = $sortParam[0] === '-' ? 'DESC' : 'ASC';
            $sorts[] = new Sort($field, $direction);
        }

        return new Sorts($sorts);
    }
}
