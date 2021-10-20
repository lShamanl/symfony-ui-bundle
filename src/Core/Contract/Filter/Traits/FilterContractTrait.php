<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Contract\Filter\Traits;

use OpenApi\Annotations as OA;

/**
 * UIBundleQuery
 */
trait FilterContractTrait
{
    /**
     * @OA\Property(type="object", example={"propertyName_1": {"like": "value_1"}, "propertyName_2": {"eq": "value_2"}})
     */
    public array $filter;
}
