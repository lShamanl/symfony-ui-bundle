<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Components;

use Bundle\UIBundle\Core\Components\Interfaces\QueryContextInterface;
use Bundle\UIBundle\Core\Contract\Command\LocalizationOutputContractInterface;
use Bundle\UIBundle\Core\Contract\Command\OutputContractInterface;
use Bundle\UIBundle\Core\Dto\Locale;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractProcessor implements ProcessorInterface
{
    protected string $responseContent = '';
    protected array $responseHeaders = [];

    public function getResponseContent(): string
    {
        return $this->responseContent;
    }

    public function makeResponse(): Response
    {
        $response = new Response();
        $response->setContent($this->responseContent);
        if (!empty($this->responseHeaders)) {
            $response->headers->add($this->responseHeaders);
        }

        return $response;
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

    protected function translate(
        object $entity,
        string $outputFormat,
        array $translations,
        Locale $locale,
        TranslatorInterface $translator,
        SerializerInterface $serializer,
    ): array {
        $entityData = (array) json_decode(
            $serializer->serialize($entity, $outputFormat),
            true
        );

        foreach ($translations as $translationDomain => $translationProperty) {
            if (isset($entityData[$translationProperty])) {
                $entityData[$translationProperty] = $translator->trans(
                    $entityData[$translationProperty],
                    [],
                    $translationDomain,
                    $locale->getPriorityLang()
                );
            }
        }

        return $entityData;
    }
}
