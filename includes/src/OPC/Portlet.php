<?php

declare(strict_types=1);

namespace JTL\OPC;

use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\L10n\GetText;
use JTL\Plugin\PluginInterface;

/**
 * Class Portlet
 * @package JTL\OPC
 */
class Portlet implements \JsonSerializable
{
    use PortletHtml;
    use PortletStyles;
    use PortletAnimations;

    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    protected string $group = '';

    /**
     * @var bool
     */
    protected bool $active = false;

    /**
     * Portlet constructor.
     * @param string               $class
     * @param int                  $id
     * @param DbInterface          $db
     * @param JTLCacheInterface    $cache
     * @param GetText              $getText
     * @param PluginInterface|null $plugin
     */
    final public function __construct(
        protected string $class,
        protected int $id,
        protected DbInterface $db,
        protected JTLCacheInterface $cache,
        protected GetText $getText,
        protected ?PluginInterface $plugin = null
    ) {
        if ($this->plugin === null) {
            $this->getText->loadAdminLocale('portlets/' . $this->class);
        } else {
            $this->getText->loadPluginLocale('portlets/' . $this->class, $this->plugin);
        }
    }

    /**
     * @param PortletInstance $instance
     * @return void
     */
    public function initInstance(PortletInstance $instance)
    {
    }

    /**
     * @return array
     */
    final public function getDefaultProps(): array
    {
        $defProps = [];
        foreach ($this->getPropertyDesc() as $name => $propDesc) {
            $defProps[$name] = $propDesc['default'] ?? '';
            if (isset($propDesc['children'])) {
                foreach ($propDesc['children'] as $childName => $childPropDesc) {
                    $defProps[$childName] = $childPropDesc['default'] ?? '';
                }
            }
            if (isset($propDesc['childrenFor'])) {
                foreach ($propDesc['childrenFor'] as $optionalPropDescs) {
                    foreach ($optionalPropDescs as $childName => $childPropDesc) {
                        $defProps[$childName] = $childPropDesc['default'] ?? '';
                    }
                }
            }
        }

        return $defProps;
    }

    /**
     * @return array{}
     */
    public function getPropertyDesc(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getExtraJsFiles(): array
    {
        return [];
    }

    /**
     * @return array<string, bool>
     */
    public function getJsFiles(): array
    {
        $list = [];
        foreach ($this->getExtraJsFiles() as $extra) {
            $list[$extra] = true;
        }
        return $list;
    }

    /**
     * @return array
     */
    public function getDeepPropertyDesc(): array
    {
        $deepDesc = [];
        foreach ($this->getPropertyDesc() as $name => $propDesc) {
            $deepDesc[$name] = $propDesc;
            if (isset($propDesc['children'])) {
                foreach ($propDesc['children'] as $childName => $childPropDesc) {
                    $deepDesc[$childName] = $childPropDesc;
                }
            }
            if (isset($propDesc['childrenFor'])) {
                foreach ($propDesc['childrenFor'] as $optionalPropDescs) {
                    foreach ($optionalPropDescs as $childName => $childPropDesc) {
                        $deepDesc[$childName] = $childPropDesc;
                    }
                }
            }
        }

        return $deepDesc;
    }

    /**
     * @return array<string, string>
     */
    public function getPropertyTabs(): array
    {
        return [];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPluginId(): int
    {
        return $this->plugin === null ? 0 : $this->plugin->getID();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return \__($this->title);
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $group
     * @return self
     */
    public function setGroup(string $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return PluginInterface|null
     */
    public function getPlugin(): ?PluginInterface
    {
        return $this->plugin;
    }

    /**
     * @param bool $active
     * @return Portlet
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'id'           => $this->getId(),
            'pluginId'     => $this->getPluginId(),
            'title'        => $this->getTitle(),
            'class'        => $this->getClass(),
            'group'        => $this->getGroup(),
            'active'       => $this->isActive(),
            'defaultProps' => $this->getDefaultProps(),
        ];
    }
}
