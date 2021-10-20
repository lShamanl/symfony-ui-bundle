<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\CQRS\Query;

use Bundle\UIBundle\Core\Components\Interfaces\QueryContextInterface;
use Bundle\UIBundle\Core\Contract\Command\LocalizationOutputContractInterface;
use Bundle\UIBundle\Core\Contract\Command\OutputContractInterface;
use Bundle\UIBundle\Core\Dto\Locale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * UIBundleQuery
 */
abstract class AbstractProcessor extends \Bundle\UIBundle\Core\Components\AbstractProcessor
{
    protected EventDispatcherInterface $dispatcher;
    protected SerializerInterface $serializer;
    protected EntityManagerInterface $entityManager;
    protected TranslatorInterface $translator;
    protected Locale $defaultLocale;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Locale $defaultLocale
    ) {
        $this->dispatcher = $dispatcher;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->defaultLocale = $defaultLocale;
    }

    protected function createOutput(QueryContextInterface $actionContext, object $entity): OutputContractInterface
    {
        $outputDtoClass = $actionContext->getOutputDtoClass();
        $outputDtoIsLocalization = is_subclass_of($outputDtoClass, LocalizationOutputContractInterface::class);
        if ($outputDtoIsLocalization && $actionContext->hasLocale()) {
            return new $outputDtoClass($entity, $actionContext->getLocale()?->getPriorityLang());
        } else {
            return new $outputDtoClass($entity);
        }
    }
}
