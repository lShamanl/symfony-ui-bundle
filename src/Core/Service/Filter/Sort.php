<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Service\Filter;

use Bundle\UIBundle\Core\Components\Exception\DomainException;

/**
 * UIBundleQuery
 */
class Sort
{
    public const SORT_ASC = 'ASC';
    public const SORT_DESC = 'DESC';

    private string $field;
    private string $direction;

    public function __construct(string $field, string $direction = self::SORT_DESC)
    {
        $this->field = $field;

        if (!in_array($direction, [self::SORT_ASC, self::SORT_DESC])) {
            throw new DomainException('Sort direction should be ASC or DESC only');
        }

        $this->direction = $direction;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}
