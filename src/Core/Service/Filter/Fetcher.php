<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Service\Filter;

use Bundle\UIBundle\Core\Components\Exception\SystemException;
use Bundle\UIBundle\Core\Dto\Filters;
use Bundle\UIBundle\Core\Dto\Sorts;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;

class Fetcher
{
    private const AGGREGATE_ALIAS = 'entity';

    private EntityManagerInterface $entityManager;
    private ?FetcherContext $context;
    private ?string $entityClass;
    private ?ClassMetadata $entityClassMetadata;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addSorts(Sorts $sorts): void
    {
        $this->guardContext();

        $this->context->filterSqlBuilder->addSorts(
            $this->context->filterAllowSorts($sorts)
        );
    }

    public function addFilters(Filters $filters): void
    {
        $this->guardContext();
        $aggregateAlias = self::AGGREGATE_ALIAS;

        AutowareFilters::autoware(
            $this->context->fetchFiltersForEntity($filters),
            $this->context->filterSqlBuilder,
            $aggregateAlias
        );

        $filtersForRelations = $this->context->fetchFiltersForRelations($filters);
        foreach ($this->context->fetchJoinList($filtersForRelations) as $propertyPath) {
            $explodePropertyPath = explode('.', $propertyPath);
            for ($level = 1; $level <= count($explodePropertyPath); $level++) {
                $relationPath = Helper::makeRelationPath($explodePropertyPath, $level);
                $path = Helper::makeAliasPathFromPropertyPath("$aggregateAlias.$relationPath");
                $alias = Helper::pathToAlias($path);

                $this->context->queryBuilder->leftJoin($path, $alias);
            }
        }

        $this->context->queryBuilder->distinct(true);

        AutowareFilters::autoware(
            $filtersForRelations,
            $this->context->filterSqlBuilder,
            $aggregateAlias
        );
    }

    public function paginate(Pagination $pagination): void
    {
        $this->guardContext();
        $this->context->filterSqlBuilder->setPagination($pagination);
    }

    public function getSearchQuery(): Query
    {
        $this->guardContext();
        return $this->context->queryBuilder->getQuery();
    }

    public function count(): int
    {
        $this->guardContext();

        $idPropertyName = current($this->context->entityClassMetadata->identifier);
        $aggregateAlias = self::AGGREGATE_ALIAS;
        return (clone $this->context->queryBuilder)
            ->select("count(distinct({$aggregateAlias}.{$idPropertyName}))")
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param class-string $entityClass
     * @return $this
     */
    public function setEntityClass(string $entityClass): self
    {
        $entityClassMetadata = $this->entityManager->getClassMetadata($entityClass);
        $entityRepository = $this->entityManager->getRepository($entityClass);
        $queryBuilder = $entityRepository->createQueryBuilder(self::AGGREGATE_ALIAS);
        $filterSqlBuilder = new FilterSqlBuilder($queryBuilder);

        $this->entityClass = $entityClass;
        $this->entityClassMetadata = $entityClassMetadata;

        $this->context = new FetcherContext(
            $this->entityManager,
            $queryBuilder,
            $entityClass,
            $entityClassMetadata,
            $filterSqlBuilder
        );

        return $this;
    }

    public function getContext(): ?FetcherContext
    {
        return $this->context;
    }

    public function getByIds(array $ids, bool $eager = true): array
    {
        $this->guardContext();
        $aggregateAlias = self::AGGREGATE_ALIAS;

        $idsPrepared = array_map(function (string $id) {
            return "'$id'";
        }, $ids);
        if (empty($idsPrepared)) {
            return [];
        }

        $qb = $this->entityManager->getRepository($this->context->entityClass)
            ->createQueryBuilder($aggregateAlias)
            ->where($aggregateAlias . ".{$this->entityClassMetadata->identifier[0]} IN (" . implode(', ', $idsPrepared) . ')');


        if ($eager) {
            $uniqueAssocRelations = array_unique(
                array_map(function (string $property) {
                    $explodeProperty = explode('.', $property);
                    array_pop($explodeProperty);
                    return implode('.', $explodeProperty);
                }, $this->context->getEntityAssociationWhiteList())
            );
            $joins = [];
            foreach ($uniqueAssocRelations as $propertyPath) {
                $explodePropertyPath = explode('.', $propertyPath);
                for ($level = 1; $level <= count($explodePropertyPath); $level++) {
                    $relationPath = Helper::makeRelationPath($explodePropertyPath, $level);
                    $path = Helper::makeAliasPathFromPropertyPath("$aggregateAlias.$relationPath");
                    $alias = Helper::pathToAlias($path);

                    if (in_array($alias, $joins)) {
                        continue;
                    }
                    $qb->leftJoin($path, $alias)->addSelect($alias);
                    $joins[] = $alias;
                }
            }
        }

        return $qb->getQuery()->getResult();
    }

    protected function guardContext(): void
    {
        if (!isset($this->context)) {
            throw new SystemException('EntityClass is not set');
        }
    }

    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    public function getEntityClassMetadata(): ?ClassMetadata
    {
        return $this->entityClassMetadata;
    }
}
