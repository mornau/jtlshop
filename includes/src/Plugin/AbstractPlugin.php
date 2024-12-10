<?php

declare(strict_types=1);

namespace JTL\Plugin;

use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Plugin\Admin\StateChanger;
use JTL\Plugin\Data\AdminMenu;
use JTL\Plugin\Data\Cache;
use JTL\Plugin\Data\Config;
use JTL\Plugin\Data\Hook;
use JTL\Plugin\Data\License;
use JTL\Plugin\Data\Links;
use JTL\Plugin\Data\Localization;
use JTL\Plugin\Data\MailTemplates;
use JTL\Plugin\Data\Meta;
use JTL\Plugin\Data\Paths;
use JTL\Plugin\Data\PaymentMethods;
use JTL\Plugin\Data\Widget;
use JTL\Shop;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractPlugin
 * @package JTL\Plugin
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * @var string
     */
    protected string $pluginID;

    /**
     * @var int
     */
    protected int $state = State::DISABLED;

    /**
     * @var Meta
     */
    protected Meta $meta;

    /**
     * @var Paths
     */
    protected Paths $paths;

    /**
     * @var int
     */
    protected int $priority = 5;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var Links
     */
    protected Links $links;

    /**
     * @var License
     */
    protected License $license;

    /**
     * @var Cache
     */
    protected Cache $cache;

    /**
     * @var bool
     */
    protected bool $isLegacy = false;

    /**
     * @var bool
     */
    protected bool $bootstrap = false;

    /**
     * @var Hook[]
     */
    protected array $hooks = [];

    /**
     * @var AdminMenu
     */
    protected AdminMenu $adminMenu;

    /**
     * @var Localization
     */
    protected Localization $localization;

    /**
     * @var Widget
     */
    protected Widget $widgets;

    /**
     * @var MailTemplates
     */
    protected MailTemplates $mailTemplates;

    /**
     * @var PaymentMethods
     */
    protected PaymentMethods $paymentMethods;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var int|null
     */
    public ?int $nCalledHook;

    /**
     * @inheritdoc
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setID(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getPluginID(): string
    {
        return $this->pluginID;
    }

    /**
     * @inheritdoc
     */
    public function setPluginID(string $pluginID): void
    {
        $this->pluginID = $pluginID;
    }

    /**
     * @inheritdoc
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @inheritdoc
     */
    public function setState(int $state): void
    {
        $this->state = $state;
    }

    /**
     * @inheritdoc
     */
    public function getMeta(): Meta
    {
        return $this->meta;
    }

    /**
     * @inheritdoc
     */
    public function setMeta(Meta $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * @inheritdoc
     */
    public function getPaths(): Paths
    {
        return $this->paths;
    }

    /**
     * @inheritdoc
     */
    public function setPaths(Paths $paths): void
    {
        $this->paths = $paths;
    }

    /**
     * @inheritdoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @inheritdoc
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @inheritdoc
     */
    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getLinks(): Links
    {
        return $this->links;
    }

    /**
     * @inheritdoc
     */
    public function setLinks(Links $links): void
    {
        $this->links = $links;
    }

    /**
     * @inheritdoc
     */
    public function getLicense(): License
    {
        return $this->license;
    }

    /**
     * @inheritdoc
     */
    public function setLicense(License $license): void
    {
        $this->license = $license;
    }

    /**
     * @inheritdoc
     */
    public function getCache(): Cache
    {
        return $this->cache;
    }

    /**
     * @inheritdoc
     */
    public function setCache(Cache $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    public function isLegacy(): bool
    {
        return $this->isLegacy;
    }

    /**
     * @inheritdoc
     */
    public function setIsLegacy(bool $isLegacy): void
    {
        $this->isLegacy = $isLegacy;
    }

    /**
     * @inheritdoc
     */
    public function isExtension(): bool
    {
        return !$this->isLegacy;
    }

    /**
     * @inheritdoc
     */
    public function setIsExtension(bool $isExtension): void
    {
        $this->isLegacy = !$isExtension;
    }

    /**
     * @inheritdoc
     */
    public function isBootstrap(): bool
    {
        return $this->bootstrap;
    }

    /**
     * @inheritdoc
     */
    public function setBootstrap(bool $bootstrap): void
    {
        $this->bootstrap = $bootstrap;
    }

    /**
     * @inheritdoc
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }

    /**
     * @inheritdoc
     */
    public function setHooks(array $hooks): void
    {
        $this->hooks = $hooks;
    }

    /**
     * @inheritdoc
     */
    public function getAdminMenu(): AdminMenu
    {
        return $this->adminMenu;
    }

    /**
     * @inheritdoc
     */
    public function setAdminMenu(AdminMenu $adminMenu): void
    {
        $this->adminMenu = $adminMenu;
    }

    /**
     * @inheritdoc
     */
    public function getLocalization(): Localization
    {
        return $this->localization;
    }

    /**
     * @inheritdoc
     */
    public function setLocalization(Localization $localization): void
    {
        $this->localization = $localization;
    }

    /**
     * @inheritdoc
     */
    public function getWidgets(): Widget
    {
        return $this->widgets;
    }

    /**
     * @inheritdoc
     */
    public function setWidgets(Widget $widgets): void
    {
        $this->widgets = $widgets;
    }

    /**
     * @inheritdoc
     */
    public function getMailTemplates(): MailTemplates
    {
        return $this->mailTemplates;
    }

    /**
     * @inheritdoc
     */
    public function setMailTemplates(MailTemplates $mailTemplates): void
    {
        $this->mailTemplates = $mailTemplates;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentMethods(): PaymentMethods
    {
        return $this->paymentMethods;
    }

    /**
     * @inheritdoc
     */
    public function setPaymentMethods(PaymentMethods $paymentMethods): void
    {
        $this->paymentMethods = $paymentMethods;
    }

    /**
     * @inheritdoc
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function selfDestruct(
        int $newState = State::DISABLED,
        DbInterface $db = null,
        JTLCacheInterface $cache = null
    ): int {
        $stateChanger = new StateChanger($db ?? Shop::Container()->getDB(), $cache ?? Shop::Container()->getCache());

        return $stateChanger->deactivate($this->getID(), $newState);
    }

    /**
     * @inheritdoc
     */
    public function updateInstance(PluginInterface $plugin): void
    {
        foreach (\get_object_vars($plugin) as $key => $val) {
            $this->$key = $val;
        }
    }
}
