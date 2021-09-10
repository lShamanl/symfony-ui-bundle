<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\CQRS\Command\Async;

use Bundle\UIBundle\Core\Components\AbstractContext;
use Bundle\UIBundle\Core\Components\AbstractProcessor;
use Bundle\UIBundle\Core\Contract\ApiFormatter;
use Bundle\UIBundle\Core\Dto\Locale;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;

class Processor extends AbstractProcessor
{
    protected EventDispatcherInterface $dispatcher;
    protected SerializerInterface $serializer;
    protected Locale $defaultLocale;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        SerializerInterface $serializer
    ) {
        $this->dispatcher = $dispatcher;
        $this->serializer = $serializer;
    }

    /**
     * @var Context $actionContext
     */
    public function process(AbstractContext $actionContext): void
    {
        $this->dispatcher->dispatch($actionContext->getCommand());

        $this->responseContent = $this->serializer->serialize(
            ApiFormatter::prepare(['ok' => true]),
            $actionContext->getOutputFormat()
        );

        $this->responseHeaders = [
            ['Content-Type' => "application/" . $actionContext->getOutputFormat()]
        ];
    }
}