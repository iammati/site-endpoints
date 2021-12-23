<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Provider;

use Site\SiteEndpoints\Configuration\RouteGroup;
use Site\SiteEndpoints\Configuration\RouteIdentifier;
use Site\SiteEndpoints\Service\EndpointsService;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EndpointsProvider
{
    protected EndpointsService $endpointsService;
    protected array $routes = [];

    public function __construct()
    {
        $this->endpointsService = GeneralUtility::makeInstance(
            EndpointsService::class,
            $GLOBALS['TYPO3_REQUEST'] ?? GeneralUtility::makeInstance(ServerRequest::class)
        );

        $this->routes = $this->endpointsService->getRoutes();
    }

    public function add(RouteIdentifier $routeIdentifier): void
    {
        $routeGroups = $routeIdentifier->getGroups();
        $groups = [];
        $routes = [];

        /** @var RouteGroup $routeGroup */
        foreach ($routeGroups as $routeGroup) {
            $groupRoutes = $routeGroup->getRoutes();

            foreach ($groupRoutes as $route) {
                $routes[] = [
                    'methods' => $route->getMethods(),
                    'routePath' => $route->getRoutePath(),
                    'callback' => $route->getCallback(),
                ];
            }

            $groups[] = [
                'routePath' => $routeGroup->getRoutePath(),
                'middlewares' => $routeGroup->getMiddlewares(),
                'extensionName' => $routeGroup->getExtensionName(),
                'pluginName' => $routeGroup->getPluginName(),
                'routes' => $routes,
            ];
        }

        $route = [
            'prefix' => $routeIdentifier->getIdentifier(),
            'groups' => $groups,
        ];

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_endpoints']['routes'][$routeIdentifier->getIdentifier()] = $route;
    }

    public function loadYaml(string $fileName)
    {
        if (!file_exists($fileName)) {
            throw new FileNotFoundException($fileName);
        }

        $routes = (new YamlFileLoader())->load($fileName)['routes'];

        foreach ($routes as $route) {
            $identifier = $route['prefix'];

            /** @todo: Throw an exception if it's set or allow overrides? */
            // if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_endpoints']['routes'][$identifier])) {
            //     throw new \Exception(
            //         sprintf(
            //             'There is a route configured already within the identifier %s',
            //             $identifier
            //         ),
            //         1628958106
            //     );
            // }

            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_endpoints']['routes'][$identifier] = $route;
        }
    }
}
