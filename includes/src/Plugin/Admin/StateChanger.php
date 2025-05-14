<?php

declare(strict_types=1);

namespace JTL\Plugin\Admin;

use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Plugin\Admin\Installation\Installer;
use JTL\Plugin\Admin\Installation\Uninstaller;
use JTL\Plugin\Admin\Validation\ValidatorInterface;
use JTL\Plugin\Helper;
use JTL\Plugin\InstallCode;
use JTL\Plugin\LegacyPluginLoader;
use JTL\Plugin\PluginInterface;
use JTL\Plugin\PluginLoader;
use JTL\Plugin\State;
use JTL\Shop;

/**
 * Class StateChanger
 * @package JTL\Plugin\Admin
 */
class StateChanger
{
    /**
     * StateChanger constructor.
     * @param DbInterface             $db
     * @param JTLCacheInterface       $cache
     * @param ValidatorInterface|null $legacyValidator
     * @param ValidatorInterface|null $pluginValidator
     */
    public function __construct(
        private readonly DbInterface $db,
        private readonly JTLCacheInterface $cache,
        private readonly ?ValidatorInterface $legacyValidator = null,
        private readonly ?ValidatorInterface $pluginValidator = null
    ) {
    }

    /**
     * @param int $pluginID
     * @return int
     * @former aktivierePlugin()
     * @throws \InvalidArgumentException
     */
    public function activate(int $pluginID): int
    {
        if ($pluginID <= 0) {
            return InstallCode::WRONG_PARAM;
        }
        $pluginData = $this->db->select('tplugin', 'kPlugin', $pluginID);
        if ($pluginData === null || empty($pluginData->kPlugin)) {
            return InstallCode::NO_PLUGIN_FOUND;
        }
        if ((int)$pluginData->bExtension === 1) {
            $path      = \PFAD_ROOT . \PLUGIN_DIR;
            $validator = $this->pluginValidator;
            $loader    = new PluginLoader($this->db, $this->cache);
        } else {
            $path      = \PFAD_ROOT . \PFAD_PLUGIN;
            $validator = $this->legacyValidator;
            $loader    = new LegacyPluginLoader($this->db, $this->cache);
        }
        if ($validator === null) {
            throw new \InvalidArgumentException('No validator set');
        }
        $valid = $validator->validateByPath($path . $pluginData->cVerzeichnis);
        if (!\in_array($valid, [InstallCode::OK, InstallCode::OK_LEGACY, InstallCode::DUPLICATE_PLUGIN_ID], true)) {
            return $valid;
        }
        $this->logStateChange($pluginID, State::ACTIVATED);
        $affectedRow = $this->db->update(
            'tplugin',
            'kPlugin',
            $pluginID,
            (object)['nStatus' => State::ACTIVATED]
        );
        $cacheTags   = [
            \CACHING_GROUP_CORE,
            \CACHING_GROUP_PLUGIN . '_' . $pluginID,
            \CACHING_GROUP_LICENSES
        ];
        $this->db->update('tadminwidgets', 'kPlugin', $pluginID, (object)['bActive' => 1]);
        $this->db->update('tlink', 'kPlugin', $pluginID, (object)['bIsActive' => 1]);
        $portlets   = $this->db->update('topcportlet', 'kPlugin', $pluginID, (object)['bActive' => 1]);
        $blueprints = $this->db->update('topcblueprint', 'kPlugin', $pluginID, (object)['bActive' => 1]);
        if ($blueprints > 0 || $portlets > 0) {
            $cacheTags[] = \CACHING_GROUP_OPC;
        }
        $this->cache->flushTags($cacheTags);
        if (($p = Helper::bootstrap($pluginID, $loader)) !== null) {
            $p->enabled();
        }

        return $affectedRow > 0 ? InstallCode::OK : InstallCode::NO_PLUGIN_FOUND;
    }

    /**
     * @param int $pluginID
     * @param int $newState
     * @return int
     * @former deaktivierePlugin()
     */
    public function deactivate(int $pluginID, int $newState = State::DISABLED): int
    {
        if ($pluginID <= 0) {
            return InstallCode::WRONG_PARAM;
        }
        $pluginData = $this->db->select('tplugin', 'kPlugin', $pluginID);
        $cacheTags  = [
            \CACHING_GROUP_CORE,
            \CACHING_GROUP_PLUGIN . '_' . $pluginID,
            \CACHING_GROUP_LICENSES,
        ];
        if (!\SAFE_MODE) {
            $loader = (int)($pluginData->bExtension ?? 0) === 1
                ? new PluginLoader($this->db, $this->cache)
                : new LegacyPluginLoader($this->db, $this->cache);
            if (($p = Helper::bootstrap($pluginID, $loader)) !== null) {
                $p->disabled();
            }
        }
        $this->logStateChange($pluginID, $newState);
        $this->db->update('tplugin', 'kPlugin', $pluginID, (object)['nStatus' => $newState]);
        $this->db->update('tlink', 'kPlugin', $pluginID, (object)['bIsActive' => 0]);
        $this->db->update('tadminwidgets', 'kPlugin', $pluginID, (object)['bActive' => 0]);
        $blueprints = $this->db->update('topcblueprint', 'kPlugin', $pluginID, (object)['bActive' => 0]);
        $portlets   = $this->db->update('topcportlet', 'kPlugin', $pluginID, (object)['bActive' => 0]);
        if ($blueprints > 0 || $portlets > 0) {
            $cacheTags[] = \CACHING_GROUP_OPC;
        }
        $this->cache->flushTags($cacheTags);

        return InstallCode::OK;
    }

    /**
     * @param PluginInterface $plugin
     * @param bool            $forceReload
     * @return int
     * @former reloadPlugin()
     * @throws \Exception
     */
    public function reload(PluginInterface $plugin, bool $forceReload = false): int
    {
        $info = $plugin->getPaths()->getBasePath() . \PLUGIN_INFO_FILE;
        if (!\file_exists($info)) {
            return -1;
        }
        $lastUpdate    = $plugin->getMeta()->getDateLastUpdate();
        $lastXMLChange = \filemtime($info);
        if ($forceReload === true || $lastXMLChange > $lastUpdate->getTimestamp()) {
            $uninstaller = new Uninstaller($this->db, $this->cache);
            $installer   = new Installer(
                $this->db,
                $uninstaller,
                $this->legacyValidator,
                $this->pluginValidator,
                $this->cache
            );
            $installer->setDir($plugin->getPaths()->getBaseDir());
            $installer->setPlugin($plugin);

            return $installer->prepare();
        }

        return 200;
    }

    protected function logStateChange(int $pluginID, int $newState): void
    {
        $exists = $this->db->getSingleObject(
            'SELECT * 
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_NAME = :tbl
                    AND TABLE_SCHEMA = :sma',
            ['sma' => \DB_NAME, 'tbl' => 'plugin_state_log']
        );
        if ($exists === null) {
            return;
        }
        $this->db->queryPrepared(
            'INSERT
            INTO plugin_state_log (adminloginID, pluginID, pluginName, stateOld, stateNew, timestamp)
            VALUES (
                :adminloginID,
                :pluginID,
                (SELECT cName FROM tplugin WHERE kPlugin = :pluginID),
                (SELECT nStatus FROM tplugin WHERE kPlugin = :pluginID),
                :stateNew,
                NOW()
            )',
            [
                'adminloginID' => Shop::Container()->getAdminAccount()->getID(),
                'pluginID'     => $pluginID,
                'stateNew'     => $newState,
            ]
        );
    }
}
