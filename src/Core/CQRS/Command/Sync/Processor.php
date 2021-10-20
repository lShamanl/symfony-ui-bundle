<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\CQRS\Command\Sync;

use Bundle\UIBundle\Core\Components\AbstractContext;
use Bundle\UIBundle\Core\Components\AbstractProcessor;
use Bundle\UIBundle\Core\Contract\ApiFormatter;
use Bundle\UIBundle\Core\Contract\Command\OutputContractInterface;
use Bundle\UIBundle\Core\Dto\Locale;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * UIBundleCommand
 */
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

    public function process(AbstractContext $actionContext): void
    {
        /** @var Context $actionContext $actionContext */
        if (!$actionContext->getLocale() instanceof Locale) {
            $actionContext->setLocale($this->defaultLocale);
        }

        /** @var OutputContractInterface|null $output */
        $output = $actionContext->getHandler()->handle(
            $actionContext->getCommand()
        );

        if (isset($output) && !empty($actionContext->getTranslations()) && !empty($actionContext->getLocale())) {
            /** @var Locale $locale */
            $locale = $actionContext->getLocale();
            $output = $this->translate(
                $output,
                $actionContext->getOutputFormat(),
                $actionContext->getTranslations(),
                $locale,
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
