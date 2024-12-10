<?php

declare(strict_types=1);

namespace JTL\OPC;

use JTL\Shop;

/**
 * Class Blueprint
 * @package JTL\OPC
 */
class Blueprint implements \JsonSerializable
{
    /**
     * @var int
     */
    protected int $id = 0;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var null|PortletInstance
     */
    protected ?PortletInstance $instance = null;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;

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
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return PortletInstance|null
     */
    public function getInstance(): ?PortletInstance
    {
        return $this->instance;
    }

    /**
     * @param PortletInstance|null $instance
     * @return $this;
     */
    public function setInstance(?PortletInstance $instance): self
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * @param array<mixed> $data
     * @return $this
     * @throws \Exception
     */
    public function deserialize(array $data): self
    {
        $this->setName($data['name']);
        $instance = Shop::Container()->getOPC()->getPortletInstance($data['content']);
        $this->setInstance($instance);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'id'       => $this->getId(),
            'name'     => $this->getName(),
            'instance' => $this->instance->jsonSerialize(),
        ];
    }
}
