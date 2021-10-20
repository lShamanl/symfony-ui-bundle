<?php

declare(strict_types=1);

namespace Bundle\UIBundle\ParamConverter;

use Bundle\UIBundle\Core\Dto\OutputFormat;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * UIBundleFoundation
 */
class OutputFormatResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === OutputFormat::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $format = $request->attributes->get('_format', 'json');
        yield new OutputFormat($format);
    }
}
