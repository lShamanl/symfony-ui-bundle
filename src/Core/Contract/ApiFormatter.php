<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Contract;

use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

class ApiFormatter
{
    /**
     * @OA\Property(type="integer", example=200)
     */
    public int $status;

    /**
     * @var array $data
     * @OA\Property(type="object")
     */
    public array $data;

    /**
     * @OA\Property(type="boolean", example=false)
     */
    public bool $isError;

    /**
     * @var array $errors
     * @OA\Property(type="object")
     */
    public array $errors;

    /**
     * @param mixed $data
     * @param int $status
     * @param mixed $errors
     * @return array
     */
    public static function prepare(
        mixed $data = [],
        int $status = Response::HTTP_OK,
        mixed $errors = []
    ): array {
        return [
            'status' => $status,
            'isError' => !empty($errors),
            'data' => $data,
            'errors' => $errors
        ];
    }
}
