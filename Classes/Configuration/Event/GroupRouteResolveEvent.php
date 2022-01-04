<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Configuration\Event;

final class GroupRouteResolveEvent
{
    /** @var array<string> */
    private array $group;
    private string $vSlug;

    /**
     * @param array<string> $group
     */
    public function __construct(array $group, string $vSlug)
    {
        $this->group = $group;
        $this->vSlug = $vSlug;
    }

    /** @return array<string> */
    public function getGroup(): array
    {
        return $this->group;
    }

    /**
     * @param array<string> $group
     */
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
