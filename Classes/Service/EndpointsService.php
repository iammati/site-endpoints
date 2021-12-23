<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

class EndpointsService
{
    protected ServerRequestInterface $request;
    protected ?array $routes = [];

    /** @throws \InvalidArgumentException */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;

        /** @var SiteInterface $site */
        $site = $request->getAttribute('site');

        if (!$site instanceof Site) {
            return false;
        }

        try {
            $routes = $site->getAttribute('routes');
        } catch (\InvalidArgumentException $e) {
            // No routes found at all inside the site configuration
            return false;
        }

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_endpoints']['routes'] += $routes;

        $this->routes = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_endpoints']['routes'];
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function resolveVslug(): ?string
    {
        $path = $this->request->getUri()->getPath();
        $segments = explode('/', $path);

        if (!isset($segments[2])) {
            return null;
        }

        $vSlug = '/'.$segments[2] ?? null;

        return $vSlug;
    }

    public function resolveCallback(string $callback): array
    {
        $splittedCallback = explode('->', $callback);

        $controllerName = $splittedCallback[0];
        $actionName = $splittedCallback[1];

        return [
            'controllerName' => $controllerName,
            'actionName' => $actionName,
        ];
    }
}
