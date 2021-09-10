<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Components\Helpers;

use Bundle\UIBundle\Core\Dto\Filters;
use Bundle\UIBundle\Core\Service\Filter\Filter;
use Bundle\UIBundle\Core\Service\Filter\FilterSqlBuilder;
use Symfony\Component\HttpFoundation\Request;

class FiltersMaker
{
    /**
     * @param Request $request
     * @return Filters
     */
    public static function make(Request $request): Filters
    {
        $filterQuery = $request->query->get('filter', []);

        $filters = [];
        if (is_array($filterQuery)) {
            foreach ($filterQuery as $property => $filterExpression) {
                if (!self::propertyIsValid($property)) { continue; }
                if (!self::filterExpressionIsValid($filterExpression)) { continue; }

                $value = current($filterExpression);
                $mode = key($filterExpression);

                if (!self::valueIsValid($value)) { continue; }
                if (!self::modeIsValid($mode)) { continue; }

                $filters[] = new Filter($property, $value, $mode);
            }
        }

        return new Filters($filters);
    }

    private static function modeIsValid(?string $mode): bool
    {
        if (!isset($mode)) {
            return false;
        }
        if (!in_array($mode, FilterSqlBuilder::MODES)) {
            return false;
        }

        return true;
    }

    private static function valueIsValid(mixed $value): bool
    {
        if (!isset($value)) {
            return false;
        }
        if (!(is_string($value) || is_array($value))) {
            return false;
        }
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                if (!is_int($key)) {
                    return false;
                }
                if (!is_string($val)) {
                    return false;
                }
            }
        }

        return true;
    }

    private static function filterExpressionIsValid(mixed $filterExpression): bool
    {
        if (!isset($filterExpression)) {
            return false;
        }
        if (empty($filterExpression)) {
            return false;
        }
        if (!is_array($filterExpression)) {
            return false;
        }

        return true;
    }

    private static function propertyIsValid(?string $property): bool
    {
        if (!isset($property)) {
            return false;
        }

        return true;
    }
}