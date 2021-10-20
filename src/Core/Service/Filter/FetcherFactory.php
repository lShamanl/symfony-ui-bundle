<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Service\Filter;

use Doctrine\ORM\EntityManagerInterface;

/**
 * UIBundleQuery
 */
class FetcherFactory
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param class-string $entityClass
     * @return Fetcher
     */
    public function forEntity(string $entityClass): Fetcher
    {
        return new Fetcher(
            $this->entityManager,
            $entityClass
        );
    }
}
