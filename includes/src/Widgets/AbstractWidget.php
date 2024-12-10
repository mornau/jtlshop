<?php

declare(strict_types=1);

namespace JTL\Widgets;

use JTL\DB\DbInterface;
use JTL\Plugin\PluginInterface;
use JTL\Shop;
use JTL\Smarty\ContextType;
use JTL\Smarty\JTLSmarty;

/**
 * Class AbstractWidget
 * @package JTL\Widgets
 */
abstract class AbstractWidget implements WidgetInterface
{
    /**
     * @var JTLSmarty
     */
    public JTLSmarty $oSmarty;

    /**
     * @var DbInterface
     */
    public DbInterface $oDB;

    /**
     * @var PluginInterface|null
     */
    public ?PluginInterface $oPlugin = null;

    /**
     * @var bool
     */
    public bool $hasBody = true;

    /**
     * @var string
     */
    public string $permission = '';

    /**
     * @inheritdoc
     */
    public function __construct(JTLSmarty $smarty = null, DbInterface $db = null, $plugin = null)
    {
        $this->oSmarty = $smarty ?? Shop::Smarty(false, ContextType::BACKEND);
        $this->oDB     = $db ?? Shop::Container()->getDB();
        $this->oPlugin = $plugin;
        $this->init();
    }

    /**
     * @return JTLSmarty
     */
    public function getSmarty(): JTLSmarty
    {
        return $this->oSmarty;
    }

    /**
     * @param JTLSmarty $oSmarty
     */
    public function setSmarty(JTLSmarty $oSmarty): void
    {
        $this->oSmarty = $oSmarty;
    }

    /**
     * @return DbInterface
     */
    public function getDB(): DbInterface
    {
        return $this->oDB;
    }

    /**
     * @param DbInterface $oDB
     */
    public function setDB(DbInterface $oDB): void
    {
        $this->oDB = $oDB;
    }

    /**
     * @return PluginInterface
     */
    public function getPlugin(): PluginInterface
    {
        return $this->oPlugin;
    }

    /**
     * @param PluginInterface $plugin
     */
    public function setPlugin(PluginInterface $plugin): void
    {
        $this->oPlugin = $plugin;
    }

    /**
     *
     */
    public function init()
    {
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getPermission(): string
    {
        return $this->permission;
    }

    /**
     * @inheritdoc
     */
    public function setPermission(string $permission): void
    {
        $this->permission = $permission;
    }

    public function hasBody(): bool
    {
        return $this->hasBody;
    }

    public function setHasBody(bool $hasBody): void
    {
        $this->hasBody = $hasBody;
    }
}
