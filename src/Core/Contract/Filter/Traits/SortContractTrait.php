<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Contract\Filter\Traits;

use OpenApi\Annotations as OA;

/**
 * UIBundleQuery
 */
trait SortContractTrait
{
    /**
     * @OA\Property(type="string", example="-createdAt,updatedAt")
     */
    public string $sort;
}
