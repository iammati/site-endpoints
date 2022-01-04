<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Configuration\Event;

final class AfterRoutesFetchedEvent
{
    /** @var array<string> */
    private array $routes;

    /**
     * @param array<string> $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /** @return array<string> */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /** @param array<string> $routes */
    public function setRoutes(array $routes): self
    {
        $this->routes = $routes;

        return $this;
    }
}
