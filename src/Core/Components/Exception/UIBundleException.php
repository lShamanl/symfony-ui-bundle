<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Components\Exception;

use DomainException;
use Throwable;

/**
 * UIBundleFoundation
 */
class UIBundleException extends DomainException
{
    public function __construct(
        string $message = "",
        ?int $code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, (int) $code, $previous);
    }
}
