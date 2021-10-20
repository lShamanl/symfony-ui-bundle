<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Dto;

/**
 * UIBundleFoundation
 */
class PropertyNameConvertDto
{
    protected array $rules = [];

    public function addConvertRule(string $contractName, string $entityProperty): self
    {
        $this->rules[$contractName] = $entityProperty;
        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }
}
