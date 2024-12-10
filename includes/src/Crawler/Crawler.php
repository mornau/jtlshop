<?php

declare(strict_types=1);

namespace JTL\Crawler;

use JTL\MagicCompatibilityTrait;

/**
 * Class Crawler
 * @package JTL\Crawler
 */
class Crawler
{
    use MagicCompatibilityTrait;

    /**
     * @var array<string, string>
     */
    protected static array $mapping = [
        'kBesucherBot'  => 'ID',
        'cName'         => 'Name',
        'cUserAgent'    => 'UserAgent',
        'cBeschreibung' => 'Description',
        'cLink'         => 'Link',
    ];

    /**
     * @var int
     */
    private int $id = 0;

    /**
     * @var string|null
     */
    private ?string $name = null;

    /**
     * @var string
     */
    private string $useragent = '';

    /**
     * @var string
     */
    private string $description = '';

    /**
     * @var string|null
     */
    private ?string $link = null;

    /**
     * @param \stdClass[] $crawlers
     * @return $this
     */
    public function map(array $crawlers): self
    {
        foreach ($crawlers as $crawler) {
            $this->setID((int)$crawler->kBesucherBot);
            $this->setDescription($crawler->cBeschreibung);
            $this->setUserAgent($crawler->cUserAgent);
            $this->setName($crawler->cName);
            $this->setLink($crawler->cLink);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setID(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->useragent;
    }

    /**
     * @param string $useragent
     */
    public function setUserAgent(string $useragent): void
    {
        $this->useragent = $useragent;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     */
    public function setLink(?string $link): void
    {
        $this->link = $link;
    }
}
