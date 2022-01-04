<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Configuration;

class RouteGroup
{
    protected ?string $extensionName = '';
    protected ?string $pluginName = '';
    protected ?string $routePath = '';
    /** @var array<string> */
    protected ?array $middlewares = [];

    /** @var Route[] */
    protected ?array $routes = [];

    public function setExtensionName(string $extensionName): self
    {
        $this->extensionName = $extensionName;

        return $this;
    }

    public function getExtensionName(): string
    {
        return $this->extensionName;
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function setPluginName(?string $pluginName): self
    {
        $this->pluginName = $pluginName;

        return $this;
    }

    public function setRoutePath(string $routePath): self
    {
        $this->routePath = $routePath;

        return $this;
    }

    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    public function addMiddleware(string $middleware): self
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function addRoute(Route $route): self
    {
        $this->routes[] = $route;

        return $this;
    }

    /** @return Route[] */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
