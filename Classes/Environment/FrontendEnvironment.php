<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Environment;

use TYPO3\CMS\Core\Error\Http\ServiceUnavailableException;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FrontendEnvironment implements SingletonInterface
{
    private ?Tsfe $tsfe = null;

    public function __construct(Tsfe $tsfe = null)
    {
        $this->tsfe = $tsfe ?? GeneralUtility::makeInstance(Tsfe::class);
    }

    /**
     * Initializes the TSFE for a given page ID and language.
     *
     * @throws SiteNotFoundException
     * @throws ServiceUnavailableException
     * @throws ImmediateResponseException
     */
    public function initializeTsfe(int $pageId, int $language = 0): void
    {
        $this->tsfe->initializeTsfe($pageId, $language);
    }
}
