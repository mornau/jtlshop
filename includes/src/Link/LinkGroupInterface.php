<?php

declare(strict_types=1);

namespace JTL\Link;

use Illuminate\Support\Collection;

/**
 * Interface LinkGroupInterface
 * @package JTL\Link
 */
interface LinkGroupInterface
{
    /**
     * @param int $id
     * @return $this
     */
    public function load(int $id): LinkGroupInterface;

    /**
     * @param \stdClass[] $groupLanguages
     * @return $this
     */
    public function map(array $groupLanguages): LinkGroupInterface;

    /**
     * @param int|null $idx
     * @return string
     */
    public function getName(int $idx = null): string;

    /**
     * @return array<int, string>
     */
    public function getNames(): array;

    /**
     * @param array<int, string> $names
     */
    public function setNames(array $names): void;

    /**
     * @return int
     */
    public function getID(): int;

    /**
     * @param int $id
     */
    public function setID(int $id): void;

    /**
     * @return Collection<LinkInterface>
     */
    public function getLinks(): Collection;

    /**
     * @return Collection<LinkInterface> - getLinks() without child links in top hierarchy
     */
    public function getHierarchy(): Collection;

    /**
     * @param Collection<LinkInterface> $links
     */
    public function setLinks(Collection $links): void;

    /**
     * @return string
     */
    public function getTemplate(): string;

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void;

    /**
     * @return int[]
     */
    public function getLanguageID(): array;

    /**
     * @param int[] $languageID
     */
    public function setLanguageID(array $languageID): void;

    /**
     * @return array<int, string>
     */
    public function getLanguageCode(): array;

    /**
     * @param array<int, string> $languageCode
     */
    public function setLanguageCode(array $languageCode): void;

    /**
     * @return bool
     */
    public function isSpecial(): bool;

    /**
     * @param bool $isSpecial
     */
    public function setIsSpecial(bool $isSpecial): void;

    /**
     * @param callable $func
     * @return Collection
     */
    public function filterLinks(callable $func): Collection;

    /**
     * @param int $langID
     * @return bool
     */
    public function isAvailableInLanguage(int $langID): bool;
}
