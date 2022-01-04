<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Configuration;

class RouteIdentifier
{
    protected ?string $identifier = '';

    /** @var RouteGroup[] */
    protected ?array $routeGroups = [];

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function addGroup(RouteGroup $routeGroup): self
    {
        $this->routeGroups[] = $routeGroup;

        return $this;
    }

    /** @return RouteGroup[] $this->routeGroups */
    public function getGroups(): array
    {
        return $this->routeGroups;
    }
}
