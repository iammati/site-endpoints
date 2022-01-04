<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Configuration;

class Route
{
    protected ?string $routePath = '';
    protected ?string $callback = '';
    /** @var array<string> */
    protected ?array $methods = ['GET'];

    public function setRoutePath(string $routePath): self
    {
        $this->routePath = $routePath;

        return $this;
    }

    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    public function setCallback(string $controllerName, string $actionName): self
    {
        $this->callback = "{$controllerName}->{$actionName}";

        return $this;
    }

    public function getCallback(): string
    {
        return $this->callback;
    }

    public function addMethod(string $method): self
    {
        $this->methods[] = $method;

        return $this;
    }

    /**
     * @param array<string> $methods
     *
     * @return Route
     */
    public function setMethods(array $methods): self
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }
}
