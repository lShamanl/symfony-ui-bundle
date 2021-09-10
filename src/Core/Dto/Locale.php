<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Dto;

class Locale
{
    /** @var string[] */
    public array $locales = [];

    public function __construct(?string $defaultLang = null)
    {
        if ($defaultLang) {
            $this->locales[] = $defaultLang;
        }
    }

    public function getPriorityLang(): string
    {
        return current($this->locales);
    }

    public function getAll(): array
    {
        return $this->locales;
    }

    public function add(string $locale): void
    {
        $this->locales[] = $locale;
    }

    /**
     * @param array<string> $languages
     */
    public function addMany(array $languages): void
    {
        $this->locales = array_unique(
            array_merge(
                $this->locales,
                $languages
            )
        );
    }
}