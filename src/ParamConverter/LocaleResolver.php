<?php

declare(strict_types=1);

namespace Bundle\UIBundle\ParamConverter;

use Bundle\UIBundle\Core\Dto\Locale;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * UIBundleFoundation
 */
class LocaleResolver implements ArgumentValueResolverInterface
{
    public const LOCALE_QUERY_PARAM = 'lang';

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === Locale::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield $this->createLocale($request);
    }

    protected function createLocale(Request $request): Locale
    {
        $locale = new Locale();
        $languages = [];
        if ($request->query->has(self::LOCALE_QUERY_PARAM)) {
            $languages[] = $request->query->get(self::LOCALE_QUERY_PARAM);
        }

        if ($preferredLanguage = $request->getPreferredLanguage()) {
            $languages[] = $preferredLanguage;
        }

        if ($acceptLanguage = $request->headers->get('Accept-Language')) {
            foreach (explode(',', $acceptLanguage) as $langItem) {
                $result = stristr($langItem, ';', true);
                $languages[] = ($result === false) ? $langItem : $result;
            }
        }
        $languages[] = $request->getDefaultLocale();

        /** @var array<int, string> $languages */
        foreach ($languages as $i => $language) {
            $languages[$i] = str_replace('_', '-', $language);
        }

        $locale->addMany(array_unique($languages));

        return $locale;
    }
}
