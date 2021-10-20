<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Service\Filter;

use Bundle\UIBundle\Core\Dto\Filters;
use Bundle\UIBundle\Core\Dto\Sorts;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;

/**
 * UIBundleQuery
 */
class FetcherContext
{
    public QueryBuilder $queryBuilder;
    public EntityManagerInterface $entityManager;
    /** @var class-string */
    public string $entityClass;
    public ClassMetadata $entityClassMetadata;
    public FilterSqlBuilder $filterSqlBuilder;
    public array $entityWhiteList = [];
    public array $entityAssociationWhiteList = [];

    /**
     * FetcherContext constructor.
     * @param EntityManagerInterface $entityManager
     * @param QueryBuilder $queryBuilder
     * @param class-string $entityClass
     * @param ClassMetadata $entityClassMetadata
     * @param FilterSqlBuilder $filterSqlBuilder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        QueryBuilder $queryBuilder,
        string $entityClass,
        ClassMetadata $entityClassMetadata,
        FilterSqlBuilder $filterSqlBuilder
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->entityClass = $entityClass;
        $this->entityClassMetadata = $entityClassMetadata;
        $this->filterSqlBuilder = $filterSqlBuilder;
        $this->entityManager = $entityManager;

        $this->calcWhiteLists();
    }

    /**
     * @param Filters $filtersForRelations
     * @return array<string>
     */
    public function fetchJoinList(Filters $filtersForRelations): array
    {
        $joinList = [];

        $entityJoinList = array_unique(
            array_map(function ($property) {
                $explodeProperty = explode('.', $property);
                array_pop($explodeProperty);
                return implode('.', $explodeProperty);
            }, $this->entityAssociationWhiteList)
        );

        foreach ($filtersForRelations->toArray() as $filter) {
            $explodeProperty = explode('.', $filter->getProperty());
            array_pop($explodeProperty);
            $assocProperty = implode('.', $explodeProperty);

            if (in_array($assocProperty, $entityJoinList)) {
                $joinList[] = $assocProperty;
            }
        }

        return $joinList;
    }

    public function fetchFiltersForEntity(Filters $filters): Filters
    {
        return new Filters(
            array_filter($filters->toArray(), function (Filter $filter) {
                return in_array($filter->getProperty(), $this->entityWhiteList);
            })
        );
    }

    public function filterAllowSorts(Sorts $sorts): Sorts
    {
        return new Sorts(
            array_filter($sorts->toArray(), function (Sort $sort) {
                return in_array($sort->getField(), $this->entityWhiteList);
            })
        );
    }

    public function fetchFiltersForRelations(Filters $filters): Filters
    {
        $filtersForRelations = [];
        foreach ($filters->toArray() as $filter) {
            if (in_array($filter->getProperty(), $this->entityAssociationWhiteList)) {
                $filtersForRelations[] = $filter;
            }
        }
        return new Filters($filtersForRelations);
    }

    private function calcWhiteLists(): void
    {
        $this->entityWhiteList = $this->fetchEntityWhiteList();
        $this->entityAssociationWhiteList = $this->fetchEntityAssociationWhiteList();
    }

    /**
     * @return array<string>
     */
    private function fetchEntityWhiteList(): array
    {
        $whiteList = [];
        foreach (array_values($this->entityClassMetadata->fieldNames) as $property) {
            $whiteList[] = $property;
        }
        return $whiteList;
    }

    /**
     * @return array<string>
     */
    private function fetchEntityAssociationWhiteList(): array
    {
        $whiteList = [];
        foreach ($this->entityClassMetadata->associationMappings as $property => $data) {
            $targetEntity = $data['targetEntity'];
            $subEntityMeta = $this->entityManager->getClassMetadata($targetEntity);
            foreach (array_values($subEntityMeta->fieldNames) as $subProperty) {
                $whiteList[] = "{$property}.{$subProperty}";
            }

            $whiteList = array_merge(
                $whiteList,
                $this->getChildAssocWhiteList($subEntityMeta->associationMappings, $property)
            );
        }

        return $whiteList;
    }

    /**
     * @param array<string, array> $associationMappings
     * @param string $prefix
     * @return array<string>
     */
    private function getChildAssocWhiteList(array $associationMappings, string $prefix): array
    {
        $whiteList = [];
        foreach ($associationMappings as $property => $data) {
            $subEntityMeta = $this->entityManager->getClassMetadata($data['targetEntity']);
            if (isset($subEntityMeta->customRepositoryClassName)) {
                continue;
            }
            foreach (array_values($subEntityMeta->fieldNames) as $subProperty) {
                $whiteList[] = "{$prefix}.{$property}.{$subProperty}";
            }
        }

        return $whiteList;
    }

    /**
     * @return array<string>
     */
    public function getEntityAssociationWhiteList(): array
    {
        return $this->entityAssociationWhiteList;
    }
}
