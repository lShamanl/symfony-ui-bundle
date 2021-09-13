<?php

declare(strict_types=1);

namespace Bundle\UIBundle\ParamConverter;

use Bundle\UIBundle\Core\Contract\Command\InputContractInterface;
use Bundle\UIBundle\Core\Service\InputContractResolver;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ContractResolver implements ArgumentValueResolverInterface
{
    private InputContractResolver $inputContractResolver;

    public function __construct(InputContractResolver $inputContractResolver)
    {
        $this->inputContractResolver = $inputContractResolver;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), InputContractInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $queryData   = $request->query->all();
        $requestData = !empty($request->getContent())
            ? json_decode((string) $request->getContent(), true)
            : $request->request->all();

        $payload = array_merge($queryData, $requestData);

        yield $this->inputContractResolver->resolve($argument->getType(), $payload);
    }
}
