<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Configuration\Event;

final class BeforeRoutesFetchedEvent
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }
}
