<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Contract;

use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

/**
 * UIBundleFoundation
 */
class ApiFormatter
{
    /**
     * @OA\Property(type="integer", example=200)
     */
    public int $status;

    /**
     * @var array<string, string> $data
     * @OA\Property(type="object")
     */
    public array $data;

    /**
     * @OA\Property(type="boolean", example=false)
     */
    public bool $isError;

    /**
     * @var array<string, string> $errors
     * @OA\Property(type="object")
     */
    public array $errors;

    /**
     * ApiFormatter constructor.
     * @param array<string, string> $data
     * @param int $status
     * @param array<string, string> $errors
     */
    public function __construct(
        array $data = [],
        int $status = Response::HTTP_OK,
        array $errors = []
    ) {
        $this->status = $status;
        $this->isError = !empty($errors);
        $this->data = $data;
        $this->errors = $errors;
    }

    public function toArray(): array
    {
        return self::prepare(
            $this->data,
            $this->status,
            $this->errors
        );
    }

    /**
     * @param mixed $data
     * @param int $status
     * @param mixed $errors
     * @return array<string, mixed>
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
