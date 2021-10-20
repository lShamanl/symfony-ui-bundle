<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Contract\Filter\Traits;

use OpenApi\Annotations as OA;

/**
 * UIBundleQuery
 */
trait PaginationContractTrait
{
    /**
     * @OA\Property(type="object", example={"number": 1, "size": 20})
     */
    public array $page;
}
