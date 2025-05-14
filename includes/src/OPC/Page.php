<?php

declare(strict_types=1);

namespace JTL\OPC;

use JTL\Helpers\GeneralObject;

/**
 * Class Page
 * @package JTL\OPC
 */
class Page implements \JsonSerializable
{
    /**
     * @var int
     */
    protected int $key = 0;

    /**
     * @var string
     */
    protected string $id = '';

    /**
     * @var bool
     */
    protected bool $isModifiable = true;

    /**
     * @var null|string
     */
    protected ?string $publishFrom = null;

    /**
     * @var null|string
     */
    protected ?string $publishTo = null;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var int
     */
    protected int $revId = 0;

    /**
     * @var string
     */
    protected string $url = '';

    /**
     * @var null|string
     */
    protected ?string $lastModified = null;

    /**
     * @var string
     */
    protected string $lockedBy = '';

    /**
     * @var null|string
     */
    protected ?string $lockedAt = null;

    /**
     * @var array|null
     */
    protected ?array $customerGroups = null;

    /**
     * @var AreaList
     */
    protected AreaList $areaList;

    /**
     * Page constructor.
     */
    public function __construct()
    {
        $this->areaList = new AreaList();
    }

    /**
     * @return int
     */
    public function getKey(): int
    {
        return $this->key;
    }

    /**
     * @param int $key
     * @return $this
     */
    public function setKey(int $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function isModifiable(): bool
    {
        return $this->isModifiable;
    }

    /**
     * @param bool $isModifiable
     * @return Page
     */
    public function setIsModifiable(bool $isModifiable): Page
    {
        $this->isModifiable = $isModifiable;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPublishFrom(): ?string
    {
        return $this->publishFrom;
    }

    /**
     * @param string|null $publishFrom
     * @return Page
     */
    public function setPublishFrom(?string $publishFrom): self
    {
        $this->publishFrom = $publishFrom;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPublishTo(): ?string
    {
        return $this->publishTo;
    }

    /**
     * @param string|null $publishTo
     * @return Page
     */
    public function setPublishTo(?string $publishTo): self
    {
        $this->publishTo = $publishTo;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Page
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getRevId(): int
    {
        return $this->revId;
    }

    /**
     * @param int $revId
     * @return Page
     */
    public function setRevId(int $revId): self
    {
        $this->revId = $revId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLastModified(): ?string
    {
        return $this->lastModified;
    }

    /**
     * @param string|null $lastModified
     * @return $this
     */
    public function setLastModified(?string $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * @return string
     */
    public function getLockedBy(): string
    {
        return $this->lockedBy;
    }

    /**
     * @param string $lockedBy
     * @return $this
     */
    public function setLockedBy(string $lockedBy): self
    {
        $this->lockedBy = $lockedBy;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLockedAt(): ?string
    {
        return $this->lockedAt;
    }

    /**
     * @param string|null $lockedAt
     * @return $this
     */
    public function setLockedAt(?string $lockedAt): self
    {
        $this->lockedAt = $lockedAt;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getCustomerGroups(): ?array
    {
        return $this->customerGroups;
    }

    /**
     * @param array|null $customerGroups
     * @return $this
     */
    public function setCustomerGroups(?array $customerGroups): self
    {
        $this->customerGroups = $customerGroups;

        return $this;
    }

    /**
     * @return AreaList
     */
    public function getAreaList(): AreaList
    {
        return $this->areaList;
    }

    /**
     * @param AreaList $newList
     * @return $this
     */
    public function setAreaList(AreaList $newList): self
    {
        $this->areaList = $newList;

        return $this;
    }

    /**
     * @param int[]|null $publicDraftKeys
     * @return int
     */
    public function getStatus(?array $publicDraftKeys = []): int
    {
        $now  = \date('Y-m-d H:i:s');
        $from = $this->getPublishFrom();
        $to   = $this->getPublishTo();

        if (!empty($from) && $now >= $from && (empty($to) || $now < $to)) {
            if (empty($publicDraftKeys) || \in_array($this->getKey(), $publicDraftKeys)) {
                return 0; // public
            }
            return 1; // planned
        }
        if (!empty($from) && $now < $from) {
            return 1; // planned
        }
        if (empty($from)) {
            return 2; // draft
        }
        if (!empty($to) && $now > $to) {
            return 3; // backdate
        }

        return -1;
    }

    /**
     * @param bool $preview
     * @return array<string, bool>
     */
    public function getCssList(bool $preview = false): array
    {
        $list = [];
        foreach ($this->areaList->getAreas() as $area) {
            /** @noinspection AdditionOperationOnArraysInspection */
            $list += $area->getCssList($preview);
        }

        return $list;
    }

    /**
     * @return array<string, bool>
     */
    public function getJsList(): array
    {
        $list = [];
        foreach ($this->areaList->getAreas() as $area) {
            /** @noinspection AdditionOperationOnArraysInspection */
            $list += $area->getJsList();
        }

        return $list;
    }

    /**
     * @param string $json
     * @return Page
     * @throws \Exception
     */
    public function fromJson(string $json): self
    {
        $this->deserialize(\json_decode($json, true));

        return $this;
    }

    /**
     * @param array<mixed> $data
     * @return Page
     * @throws \Exception
     */
    public function deserialize(array $data): self
    {
        $this->setKey($data['key'] ?? $this->getKey());
        $this->setId($data['id'] ?? $this->getId());
        $this->setPublishFrom($data['publishFrom'] ?? $this->getPublishFrom());
        $this->setPublishTo($data['publishTo'] ?? $this->getPublishTo());
        $this->setName($data['name'] ?? $this->getName());
        $this->setUrl($data['url'] ?? $this->getUrl());
        $this->setRevId($data['revId'] ?? $this->getRevId());
        $this->setCustomerGroups($data['customerGroups'] ?? $this->getCustomerGroups());

        if (GeneralObject::isCountable('areas', $data)) {
            $this->getAreaList()->deserialize($data['areas']);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'key'            => $this->getKey(),
            'id'             => $this->getId(),
            'publishFrom'    => $this->getPublishFrom(),
            'publishTo'      => $this->getPublishTo(),
            'name'           => $this->getName(),
            'revId'          => $this->getRevId(),
            'url'            => $this->getUrl(),
            'lastModified'   => $this->getLastModified(),
            'lockedBy'       => $this->getLockedBy(),
            'lockedAt'       => $this->getLockedAt(),
            'areaList'       => $this->getAreaList()->jsonSerialize(),
            'customerGroups' => $this->getCustomerGroups(),
        ];
    }
}
