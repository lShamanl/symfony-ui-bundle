<?php

declare(strict_types=1);

namespace Bundle\UIBundle\ParamConverter;

use Bundle\UIBundle\Core\Components\Helpers\PaginationMaker;
use Bundle\UIBundle\Core\Service\Filter\Pagination;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * UIBundleQuery
 */
class PaginationResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === Pagination::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield PaginationMaker::make($request);
    }
}
