<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\CQRS\Query\GetOne;

use Bundle\UIBundle\Core\Components\AbstractContext;
use Bundle\UIBundle\Core\Contract\ApiFormatter;
use Bundle\UIBundle\Core\CQRS\Query\AbstractProcessor;
use Bundle\UIBundle\Core\Dto\Locale;
use Doctrine\ORM\EntityNotFoundException;

class Processor extends AbstractProcessor
{
    /**
     * @param Context $actionContext
     * @throws EntityNotFoundException
     */
    public function process(AbstractContext $actionContext): void
    {
        if (!$actionContext->getLocale() instanceof Locale) {
            $actionContext->setLocale($this->defaultLocale);
        }

        #todo: тут можно сделать жадную загрузку как в Search по ID
        $entity = $this->getEntityById(
            $actionContext->getEntityId(),
            $actionContext->getTargetEntityClass()
        );

        $output = $this->createOutput($actionContext, $entity);

        if (!empty($actionContext->getTranslations())) {
            $output = $this->translate(
                $output,
                $actionContext->getOutputFormat(),
                $actionContext->getTranslations(),
                $actionContext->getLocale(),
                $this->translator
            );
        }

        $this->responseContent = $this->serializer->serialize(
            ApiFormatter::prepare(['entity' => $output]),
            $actionContext->getOutputFormat()
        );
        $this->responseHeaders = [
            ['Content-Type' => "application/" . $actionContext->getOutputFormat()]
        ];
    }

    protected function getEntityById(string $id, string $entityClass): object
    {
        if (!$entity = $this->entityManager->getRepository($entityClass)->findOneBy(['id' => $id])) {
            $classnameExplode = explode('\\', $entityClass);
            $classname = end($classnameExplode);
            throw new EntityNotFoundException("{$classname} with id {$id} not exist");
        }

        return $entity;
    }
}
