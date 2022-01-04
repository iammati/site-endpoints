<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * Service class to resolve a valid site language from the "Accept-Language HTTP" header
 * of a given request. See what configured SiteLanguage matches best, based on locale/twoLetterIsoCode
 * value of the configured site language in the site configuration.
 */
class RequestedLanguageToSiteLanguageResolverService
{
    public function __invoke(ServerRequestInterface $request): ?SiteLanguage
    {
        /** @var SiteInterface $site */
        $site = $request->getAttribute('site');

        if (!$site instanceof Site) {
            return null;
        }

        $requestedLanguages = $this->fetchLanguagePreferencesFromRequest($request);

        foreach ($requestedLanguages as $requestLanguageCode) {
            /** @var SiteLanguage $language */
            foreach ($site->getLanguages() as $language) {
                [$locale] = explode('.', strtolower($language->getLocale()));

                if ($requestLanguageCode === $locale || $requestLanguageCode === $language->getTwoLetterIsoCode()) {
                    return $language;
                }
            }
        }

        return $site->getDefaultLanguage();
    }

    /**
     * @return array<mixed>
     */
    protected function fetchLanguagePreferencesFromRequest(ServerRequestInterface $request): array
    {
        $requestedLanguages = explode(',', $request->getHeaderLine('accept-language') ? $request->getHeaderLine('accept-language') : 'en');

        return array_map(function ($language) {
            [$locale] = explode(';', $language);

            return strtolower(str_replace('-', '_', $locale));
        }, $requestedLanguages);
    }
}
