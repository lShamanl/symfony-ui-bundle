<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Components;

use Bundle\UIBundle\Core\Dto\Locale;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * UIBundleFoundation
 */
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

    /**
     * @throws \JsonException
     */
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
            true,
            512,
            JSON_THROW_ON_ERROR
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
