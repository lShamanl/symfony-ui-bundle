<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Service;

use Bundle\UIBundle\Core\Components\Exception\DomainException;
use Bundle\UIBundle\Core\Contract\Command\InputContractInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * UIBundleCommand
 */
class InputContractResolver
{
    private ValidatorService $validator;
    private SerializerInterface $serializer;

    public function __construct(
        ValidatorService $validator,
        SerializerInterface $serializer
    ) {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * @param class-string<InputContractInterface> $contractClass
     * @param array<string, string> $payload
     * @return InputContractInterface
     */
    public function resolve(string $contractClass, array $payload): InputContractInterface
    {
        if (!is_subclass_of($contractClass, InputContractInterface::class)) {
            throw new DomainException("{$contractClass} not is subclass of " . InputContractInterface::class);
        }

        $inputContractDto = $this->serializer->deserialize(
            json_encode($payload),
            $contractClass,
            'json'
        );

        $this->validator->validate($inputContractDto);

        return $inputContractDto;
    }
}
