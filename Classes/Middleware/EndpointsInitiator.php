<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Site\SiteEndpoints\Configuration\Event\GroupRouteResolveEvent;
use Site\SiteEndpoints\Factory\AppFactory;
use Site\SiteEndpoints\Provider\EndpointsProvider;
use Site\SiteEndpoints\Service\EndpointsService;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Checks the site configuration if there are any routes configured that we could handle here.
 * The main key is the "prefix" in your site configuration "routes" option.
 */
class EndpointsInitiator implements MiddlewareInterface
{
    protected ?EndpointsProvider $endpointsProvider = null;
    protected ?EndpointsService $endpointsService = null;
    protected ?EventDispatcher $eventDispatcher = null;

    public function __construct(EventDispatcher $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher ?? GeneralUtility::makeInstance(EventDispatcher::class);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Defining the globals here so it's accessible from the EndpointsProvider
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $this->endpointsProvider = GeneralUtility::makeInstance(EndpointsProvider::class);
        $this->endpointsService = GeneralUtility::makeInstance(EndpointsService::class, $request, $handler);

        // Unsetting so TYPO3's bootstrap can set/define it properly
        unset($GLOBALS['TYPO3_REQUEST']);

        $routes = $this->endpointsService->getRoutes();

        // No routes configured in the site configuration
        if (empty($routes)) {
            return $handler->handle($request);
        }

        $vSlug = $this->endpointsService->resolveVslug();

        if ($vSlug === null) {
            return $handler->handle($request);
        }

        // Resolving a route-group by the requested prefix + vSlug
        foreach ($routes as $config) {
            $prefix = $config['prefix'] ?? '/';

            if (strpos($request->getUri()->getPath(), $prefix) !== 0) {
                continue;
            }

            unset($config['prefix']);

            foreach ($config['groups'] as $group) {
                $event = new GroupRouteResolveEvent($group, $vSlug);
                $dispatchedEvent = $this->eventDispatcher->dispatch($event);
                $group = $dispatchedEvent->getGroup();
                $vSlug = $dispatchedEvent->getVSlug();

                $routePath = $group['routePath'];

                if ($vSlug === 'SITE_ENDPOINTS_SKIP') {
                    continue;
                }

                if (in_array($routePath, [
                    $vSlug,
                    '/*',
                    'SITE_ENDPOINTS_SKIP',
                ])) {
                    /** @var AppFactory $appFactory */
                    $appFactory = GeneralUtility::makeInstance(
                        AppFactory::class
                    )->create(
                        $request,
                        $handler,
                        array_merge(
                            ['prefix' => $prefix],
                            $group,
                        )
                    );

                    if ($appFactory instanceof ResponseInterface) {
                        return $appFactory;
                    }

                    if (!$appFactory instanceof AppFactory) {
                        return $handler->handle($request);
                    }

                    return $appFactory->handle();
                }
            }
        }

        return $handler->handle($request);
    }
}
