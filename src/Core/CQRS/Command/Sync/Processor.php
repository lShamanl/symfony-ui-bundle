<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\CQRS\Command\Sync;

use Bundle\UIBundle\Core\Components\AbstractContext;
use Bundle\UIBundle\Core\Components\AbstractProcessor;
use Bundle\UIBundle\Core\Contract\ApiFormatter;
use Bundle\UIBundle\Core\Dto\Locale;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Processor extends AbstractProcessor
{
    protected SerializerInterface $serializer;
    protected TranslatorInterface $translator;
    protected Locale $defaultLocale;

    public function __construct(
        SerializerInterface $serializer,
        TranslatorInterface $translator,
        Locale $defaultLocale
    ) {
        $this->serializer = $serializer;
        $this->translator = $translator;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param Context $actionContext
     */
    public function process(AbstractContext $actionContext): void
    {
        if (!$actionContext->getLocale() instanceof Locale) {
            $actionContext->setLocale($this->defaultLocale);
        }

        $output = $actionContext->getHandler()->handle(
            $actionContext->getCommand()
        );

        if (!empty($actionContext->getTranslations()) && !empty($actionContext->getLocale())) {
            $output = $this->translate(
                $output,
                $actionContext->getOutputFormat(),
                $actionContext->getTranslations(),
                $actionContext->getLocale(),
                $this->translator,
                $this->serializer,
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
}
