<?php

declare(strict_types=1);

namespace Bundle\UIBundle\ParamConverter;

use Bundle\UIBundle\Core\Components\Helpers\SortsMaker;
use Bundle\UIBundle\Core\Dto\Sorts;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * UIBundleQuery
 */
class SortsResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === Sorts::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield SortsMaker::make($request);
    }
}
