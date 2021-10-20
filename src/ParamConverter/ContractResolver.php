<?php

declare(strict_types=1);

namespace Bundle\UIBundle\ParamConverter;

use Bundle\UIBundle\Core\Contract\Command\InputContractInterface;
use Bundle\UIBundle\Core\Service\InputContractResolver;
use Bundle\UIBundle\Core\Service\RequestParser;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * UIBundleCommand
 */
class ContractResolver implements ArgumentValueResolverInterface
{
    private InputContractResolver $inputContractResolver;
    private RequestParser $requestParser;

    public function __construct(
        InputContractResolver $inputContractResolver,
        RequestParser $requestParser
    ) {
        $this->inputContractResolver = $inputContractResolver;
        $this->requestParser = $requestParser;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $type = $argument->getType();
        return $type !== null && is_subclass_of($type, InputContractInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        /** @var class-string<InputContractInterface> $type */
        $type = $argument->getType();

        yield $this->inputContractResolver->resolve(
            $type,
            $this->requestParser->parse($request)
        );
    }
}
