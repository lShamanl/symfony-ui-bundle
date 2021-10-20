<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Components;

use Symfony\Component\HttpFoundation\Response;

/**
 * UIBundleFoundation
 */
interface ProcessorInterface
{
    public function process(AbstractContext $actionContext): void;
    public function getResponseContent(): string;
    public function makeResponse(): Response;
}
