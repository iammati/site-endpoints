<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Configuration\Event;

final class GroupRouteResolveEvent
{
    private array $group;
    private string $vSlug;

    public function __construct(array $group, string $vSlug)
    {
        $this->group = $group;
        $this->vSlug = $vSlug;
    }

    public function getGroup(): array
    {
        return $this->group;
    }

    public function setGroup(array $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getVSlug(): string
    {
        return $this->vSlug;
    }

    public function setVSlug(string $vSlug): self
    {
        $this->vSlug = $vSlug;

        return $this;
    }
}
