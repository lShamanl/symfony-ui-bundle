<?php

declare(strict_types=1);

namespace Bundle\UIBundle\ParamConverter;

use Bundle\UIBundle\Core\Components\Helpers\FiltersMaker;
use Bundle\UIBundle\Core\Dto\Filters;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * UIBundleQuery
 */
class FiltersResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === Filters::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield FiltersMaker::make($request);
    }
}
