<?php

declare(strict_types=1);

namespace JTL\Link;

use Illuminate\Support\Collection;

/**
 * Interface LinkListInterface
 * @package JTL\Link
 */
interface LinkListInterface
{
    /**
     * @param int[] $linkIDs
     * @return Collection<LinkInterface>
     */
    public function createLinks(array $linkIDs): Collection;

    /**
     * @return Collection<LinkInterface>
     */
    public function getLinks(): Collection;

    /**
     * @param Collection<LinkInterface> $links
     */
    public function setLinks(Collection $links): void;

    /**
     * @param LinkInterface $link
     */
    public function addLink(LinkInterface $link): void;
}
