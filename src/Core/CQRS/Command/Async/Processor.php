<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\CQRS\Command\Async;

use Bundle\UIBundle\Core\Components\AbstractContext;
use Bundle\UIBundle\Core\Components\AbstractProcessor;
use Bundle\UIBundle\Core\Contract\ApiFormatter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * UIBundleCommand
 */
class Processor extends AbstractProcessor
{
    protected EventDispatcherInterface $dispatcher;
    protected SerializerInterface $serializer;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        SerializerInterface $serializer
    ) {
        $this->dispatcher = $dispatcher;
        $this->serializer = $serializer;
    }

    public function process(AbstractContext $actionContext): void
    {
        /** @var Context $actionContext */
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
