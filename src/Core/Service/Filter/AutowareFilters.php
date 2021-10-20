<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Service\Filter;

use Bundle\UIBundle\Core\Dto\Filters;

/**
 * UIBundleQuery
 */
class AutowareFilters
{
    public static function autoware(Filters $filters, FilterSqlBuilder $appSqlBuilder, string $rootEntityAlias): void
    {
        foreach ($filters->toArray() as $filter) {
            self::applyFilter($appSqlBuilder, $filter, $rootEntityAlias);
        }
    }

    public static function applyFilter(
        FilterSqlBuilder $appSqlBuilder,
        Filter $filter,
        string $fieldPrefix
    ): void {
        $aliasPath = Helper::makeAliasPathFromPropertyPath("$fieldPrefix.{$filter->getProperty()}");
        /** @var array|string|int|null $value */
        $value = $filter->getValue();

        switch (mb_strtolower($filter->getSearchMode())) {
            case FilterSqlBuilder::NOT_IN:
                if (isset($value) && is_array($value)) {
                    $appSqlBuilder->notIn($aliasPath, $value);
                }
                break;
            case FilterSqlBuilder::IN:
                if (isset($value) && is_array($value)) {
                    $appSqlBuilder->in($aliasPath, $value);
                }
                break;
            case FilterSqlBuilder::RANGE:
                if (isset($value) && is_string($value)) {
                    self::rangeDecorator($appSqlBuilder, $value, $aliasPath);
                }
                break;
            case FilterSqlBuilder::IS_NULL:
                $appSqlBuilder->isNull($aliasPath);
                break;
            case FilterSqlBuilder::NOT_NULL:
                $appSqlBuilder->notNull($aliasPath);
                break;
            case '<':
            case 'lt':
            case FilterSqlBuilder::LESS_THAN:
                $appSqlBuilder->lessThan($aliasPath, $value);
                break;
            case '>':
            case 'gt':
            case FilterSqlBuilder::GREATER_THAN:
                $appSqlBuilder->greaterThan($aliasPath, $value);
                break;
            case '<=':
            case 'lte':
            case FilterSqlBuilder::LESS_OR_EQUALS:
                $appSqlBuilder->lessOrEquals($aliasPath, $value);
                break;
            case '>=':
            case 'gte':
            case FilterSqlBuilder::GREATER_OR_EQUALS:
                $appSqlBuilder->greaterOrEquals($aliasPath, $value);
                break;
            case FilterSqlBuilder::LIKE:
                $appSqlBuilder->like($aliasPath, $value);
                break;
            case FilterSqlBuilder::NOT_LIKE:
                $appSqlBuilder->notLike($aliasPath, $value);
                break;
            case '=':
            case 'eq':
            case FilterSqlBuilder::EQUALS:
                $appSqlBuilder->equals($aliasPath, $value);
                break;
            case '!=':
            case '<>':
            case 'neq':
            case FilterSqlBuilder::NOT_EQUALS:
                $appSqlBuilder->notEquals($aliasPath, $value);
                break;
        }
    }

    protected static function rangeDecorator(
        FilterSqlBuilder $appSqlBuilder,
        string $field,
        string $value
    ): FilterSqlBuilder {
        $parseData = explode(',', $value);
        $gte = $parseData[0];
        $lte = $parseData[1];
        return $appSqlBuilder->range($field, $gte, $lte);
    }
}
