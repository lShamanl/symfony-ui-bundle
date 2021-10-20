<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Service\Filter;

use Bundle\UIBundle\Core\Dto\Sorts;
use DateTimeInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * UIBundleQuery
 */
class FilterSqlBuilder
{
    public const NOT_IN = 'not-in';
    public const IN = 'in';
    public const RANGE = 'range';
    public const IS_NULL = 'is-null';
    public const NOT_NULL = 'not-null';
    public const LESS_THAN = 'less-than';
    public const GREATER_THAN = 'greater-than';
    public const LESS_OR_EQUALS = 'less-or-equals';
    public const GREATER_OR_EQUALS = 'greater-or-equals';
    public const LIKE = 'like';
    public const NOT_LIKE = 'not-like';
    public const EQUALS = 'equals';
    public const NOT_EQUALS = 'not-equals';

    public const MODES = [
        self::NOT_IN,
        self::IN,
        self::RANGE,
        self::IS_NULL,
        self::NOT_NULL,
        self::LESS_THAN,
        self::GREATER_THAN,
        self::LESS_OR_EQUALS,
        self::GREATER_OR_EQUALS,
        self::LIKE,
        self::NOT_LIKE,
        self::EQUALS,
        self::NOT_EQUALS,
    ];

    private QueryBuilder $queryBuilder;
    private string $alias;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->alias = current($this->queryBuilder->getAllAliases());
    }

    public function addSorts(Sorts $sorts): self
    {
        foreach ($sorts->toArray() as $sort) {
            $this->addSort($sort);
        }

        return $this;
    }

    public function addSort(?Sort $sort): self
    {
        if ($sort) {
            $this->queryBuilder->addOrderBy("{$this->alias}.{$sort->getField()}", $sort->getDirection());
        }

        return $this;
    }

    public function setPagination(Pagination $pagination): self
    {
        $this->queryBuilder
            ->setFirstResult($pagination->getOffset())
            ->setMaxResults($pagination->getPageSize())
        ;

        return $this;
    }

    public function equals(string $field, mixed $value): self
    {
        if ($value !== null) {
            $value = is_bool($value) ? (int) $value : $value;
            $bind = $this->bind($value);
            $this->queryBuilder->andWhere("{$field} = :{$bind}");
        }

        return $this;
    }

    public function notEquals(string $field, mixed $value): self
    {
        if ($value !== null) {
            $value = is_bool($value) ? (int) $value : $value;
            $bind = $this->bind($value);
            $this->queryBuilder->andWhere("{$field} != :{$bind}");
        }

        return $this;
    }

    public function like(string $field, mixed $value): self
    {
        if (!empty($value)) {
            $bind = $this->bind("%{$value}%");
            $this->queryBuilder->andWhere("{$field} LIKE :{$bind}");
        }

        return $this;
    }

    public function isNull(string $field): self
    {
        $this->queryBuilder->andWhere("{$field} IS NULL");
        return $this;
    }

    public function notNull(string $field): self
    {
        $this->queryBuilder->andWhere("{$field} IS NOT NULL");
        return $this;
    }

    public function notLike(string $field, mixed $value): self
    {
        if (!empty($value)) {
            $bind = $this->bind("{$value}");
            $this->queryBuilder->andWhere("{$field} NOT LIKE :{$bind}");
        }

        return $this;
    }

    public function in(string $field, ?array $values): self
    {
        if (!empty($values)) {
            $bind = $this->bind($values);
            $this->queryBuilder->andWhere("{$field} IN (:{$bind})");
        }

        return $this;
    }

    public function notIn(string $field, ?array $values): self
    {
        if (!empty($values)) {
            $bind = $this->bind($values);
            $this->queryBuilder->andWhere("{$field} NOT IN (:{$bind})");
        }

        return $this;
    }

    public function lessThan(string $field, mixed $lte): self
    {
        if (!empty($values)) {
            $lteBind = $this->bind($lte);
            $this->queryBuilder->andWhere("{$field} < :{$lteBind}");
        }

        return $this;
    }

    public function greaterThan(string $field, mixed $gte): self
    {
        if (!empty($values)) {
            $gteBind = $this->bind($gte);
            $this->queryBuilder->andWhere("{$field} > :{$gteBind}");
        }

        return $this;
    }

    public function lessOrEquals(string $field, mixed $lte): self
    {
        if (!empty($values)) {
            $lteBind = $this->bind($lte);
            $this->queryBuilder->andWhere("{$field} <= :{$lteBind}");
        }

        return $this;
    }

    public function greaterOrEquals(string $field, mixed $gte): self
    {
        if (!empty($values)) {
            $gteBind = $this->bind($gte);
            $this->queryBuilder->andWhere("{$field} >= :{$gteBind}");
        }

        return $this;
    }

    public function range(string $field, mixed $gte, mixed $lte): self
    {
        if ($gte !== null && $lte !== null) {
            $gteBind = $this->bind($gte);
            $lteBind = $this->bind($lte);
            $this->queryBuilder->andWhere("{$field} BETWEEN :{$gteBind} AND :{$lteBind}");
        } elseif ($gte !== null) {
            $gteBind = $this->bind($gte);
            $this->queryBuilder->andWhere("{$field} >= :{$gteBind}");
        } elseif ($lte !== null) {
            $lteBind = $this->bind($lte);
            $this->queryBuilder->andWhere("{$field} <= :{$lteBind}");
        }
        return $this;
    }

    public function rangeDateTime(
        string $field,
        ?DateTimeInterface $gte,
        ?DateTimeInterface $lte
    ): self {
        $this->range(
            $field,
            $gte ? $gte->format('Y-m-d H:i:s') : null,
            $lte ? $lte->format('Y-m-d H:i:s') : null
        );

        return $this;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    private function bind(mixed $value): string
    {
        $bind = 'bind_' . md5(serialize($value));

        if (is_array($value)) {
            $this->queryBuilder->setParameter($bind, implode(',', $value));
        } else {
            $this->queryBuilder->setParameter($bind, $value);
        }

        return $bind;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }
}
