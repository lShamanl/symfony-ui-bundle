<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Core\Dto;

/**
 * UIBundleFoundation
 */
class Locale
{
    public const DEFAULT_LANG = 'en';

    /** @var string[] */
    public array $locales = [];

    public function __construct(string $defaultLang = self::DEFAULT_LANG)
    {
        $this->locales[] = $defaultLang;
    }

    public function getPriorityLang(): string
    {
        return !empty($this->locales)
            ? current($this->locales)
            : self::DEFAULT_LANG
        ;
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
