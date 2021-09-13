<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\CQRS\Query;

use Bundle\UIBundle\Core\Dto\Locale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
}
