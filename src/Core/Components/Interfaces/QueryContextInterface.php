<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Components\Interfaces;

use Bundle\UIBundle\Core\Contract\Command\OutputContractInterface;
use Bundle\UIBundle\Core\Dto\Locale;

/**
 * UIBundleQuery
 */
interface QueryContextInterface
{
    public function hasLocale(): bool;
    public function getLocale(): ?Locale;
    public function setLocale(Locale $locale): self;

    /**
     * @return class-string<OutputContractInterface>
     */
    public function getOutputDtoClass(): string;

    /**
     * @param class-string<OutputContractInterface> $outputDtoClass
     * @return self
     */
    public function setOutputDtoClass(string $outputDtoClass): self;
}
