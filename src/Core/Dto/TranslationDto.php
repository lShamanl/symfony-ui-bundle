<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Dto;

/**
 * UIBundleQuery
 */
class TranslationDto
{
    protected array $rules = [];

    public function addTranslationRules(string $domain, string $entityProperty): self
    {
        $this->rules[$domain] = $entityProperty;
        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }
}
