<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\CQRS\Query\Search;

use Bundle\UIBundle\Core\Components\AbstractContext;
use Bundle\UIBundle\Core\Contract\ApiFormatter;
use Bundle\UIBundle\Core\CQRS\Query\AbstractProcessor;
use Bundle\UIBundle\Core\Dto\Filters;
use Bundle\UIBundle\Core\Dto\Locale;
use Bundle\UIBundle\Core\Service\Filter\FetcherFactory;
use Bundle\UIBundle\Core\Service\Filter\Filter;
use Closure;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * UIBundleQuery
 */
class Processor extends AbstractProcessor
{
    private FetcherFactory $fetcherFactory;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Locale $defaultLocale,
        FetcherFactory $fetcherFactory
    ) {
        parent::__construct($dispatcher, $serializer, $entityManager, $translator, $defaultLocale);
        $this->fetcherFactory = $fetcherFactory;
    }

    public function deleteFilterInBlackList(Filters $filters, array $blackList): Filters
    {
        $filterList = array_filter($filters->toArray(), static function (Filter $filter) use ($blackList) {
            return !in_array($filter->getProperty(), $blackList);
        });

        return new Filters($filterList);
    }

    public function applyFilterFieldAliases(Filters $filters, array $aliases): void
    {
        array_map(static function (Filter $filter) use ($aliases) {
            if (array_key_exists($filter->getProperty(), $aliases)) {
                $filter->setPropertyName(
                    $aliases[$filter->getProperty()]
                );
            }
        }, $filters->toArray());
    }

    /**
     * @param Context $actionContext
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     */
    public function process(AbstractContext $actionContext): void
    {
        if (!$actionContext->getLocale() instanceof Locale) {
            $actionContext->setLocale($this->defaultLocale);
        }

        $fetcher = $this->fetcherFactory->forEntity($actionContext->getTargetEntityClass());
        $filters = $this->extractFilters($actionContext);
        $sorts = $actionContext->getSorts();
        $pagination = $actionContext->getPagination();

        $fetcher->addFilters($filters);
        $count = $fetcher->count();

        $fetcher->addSorts($sorts);
        $fetcher->paginate($pagination);

        $searchResult = $fetcher->getByIds(
            $fetcher->searchEntityIds(),
            $actionContext->getEagerMode()
        );

        $this->applyEntityCallback(
            $actionContext->getEntityCallback(),
            $searchResult
        );

        $entities = [];
        foreach ($searchResult as $entity) {
            $entities[] = $this->createOutput($actionContext, $entity);
        }

        if (!empty($actionContext->getTranslations()) && $actionContext->getLocale() !== null) {
            $entities = array_map(function (object $entity) use ($actionContext) {
                return $this->translate(
                    $entity,
                    $actionContext->getOutputFormat(),
                    $actionContext->getTranslations(),
                    $actionContext->getLocale(),
                    $this->translator,
                    $this->serializer,
                );
            }, $entities);
        }

        $this->responseContent = $this->serializer->serialize(
            ApiFormatter::prepare([
                'entities' => $entities,
                'pagination' => [
                    'count' => $count,
                    'totalPages' => (int) ceil($count / $pagination->getPageSize()),
                    'page' => $pagination->getPageNumber(),
                    'size' => count($entities),
                ]
            ]),
            $actionContext->getOutputFormat()
        );
        $this->responseHeaders = [
            ['Content-Type' => "application/" . $actionContext->getOutputFormat()]
        ];
    }

    private function extractFilters(AbstractContext $actionContext): Filters
    {
        /** @var Context $actionContext */
        $filters = $actionContext->getFilters();
        $this->applyFilterFieldAliases($filters, $actionContext->getFilterAliases());
        return $this->deleteFilterInBlackList($filters, $actionContext->getFilterBlackList());
    }

    /**
     * @param Closure|null $callback
     * @param array<int, object> $entities
     */
    private function applyEntityCallback(?Closure $callback, array $entities): void
    {
        if ($callback !== null) {
            foreach ($entities as $entity) {
                $callback($entity);
            }
        }
    }
}
