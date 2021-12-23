<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;
use Site\SiteEndpoints\Environment\FrontendEnvironment;
use Site\SiteEndpoints\Service\EndpointsService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Middleware\FrontendUserAuthenticator;

/**
 * Extbase and TSFE initializer
 */
class ExtbaseBridge implements MiddlewareInterface
{
    protected FrontendEnvironment $frontendEnvironment;
    protected FrontendUserAuthenticator $frontendUserAuthenticator;
    protected ?EndpointsService $endpointsService = null;

    public function __construct()
    {
        $this->frontendEnvironment = GeneralUtility::makeInstance(FrontendEnvironment::class);
        $this->frontendUserAuthenticator = GeneralUtility::makeInstance(FrontendUserAuthenticator::class);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $site = $request->getAttribute('site');

        if (!$site instanceof Site) {
            return $handler->handle($request);
        }

        if (!isset($GLOBALS['TSFE']) || !$GLOBALS['TSFE'] instanceof TypoScriptFrontendController) {
            $this->createGlobalTsfe($site);
        } else {
            $GLOBALS['TSFE']->id = $site->getRootPageId();
        }

        // Booting up TypoScriptFrontendController (TSFE)
        $this->bootFrontend();

        // Booting up Extbase for controller actions
        $this->endpointsService = GeneralUtility::makeInstance(
            EndpointsService::class,
            $request,
            $handler,
        );

        $this->bootExtbase();

        return $handler->handle($request);
    }

    protected function createGlobalTsfe(Site $site): void
    {
        $this->frontendEnvironment->initializeTsfe($site->getRootPageId(), 0);
    }

    protected function bootFrontend(): void
    {
        $GLOBALS['TSFE']->fetch_the_id();
        $GLOBALS['TSFE']->getConfigArray();
        $GLOBALS['TSFE']->settingLanguage($GLOBALS['TYPO3_REQUEST']);
        $GLOBALS['TSFE']->newCObj();
    }

    protected function bootExtbase(): void
    {
        GeneralUtility::makeInstance(Bootstrap::class)->initialize([
            'extensionName' => 'SiteEndpoints',
            'vendorName' => 'Site',
            'pluginName' => 'Pi1',
        ]);

        $routes = $this->endpointsService->getRoutes();
        $callbacks = [];

        foreach ($routes as $route) {
            foreach ($route['groups'] as $group) {
                foreach ($group['routes'] as $subroute) {
                    $cbData = $this->endpointsService->resolveCallback($subroute['callback']);

                    $controllerName = $cbData['controllerName'];
                    $actionName = $cbData['actionName'];

                    $callbacks[
                        (new ReflectionClass($controllerName))->getName()
                    ] = $actionName;
                }
            }
        }

        // Registering all controllers into the Endpoints plugin
        ExtensionUtility::configurePlugin(
            'SiteEndpoints',
            'Pi1',
            $callbacks,
            $callbacks
        );
    }
}
