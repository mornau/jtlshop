<?php

/**
 * Integrate user backendextension plugin in shop core
 *
 * @author mh
 * @created Wed, 04 Dec 2019 12:51:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Plugin\Admin\StateChanger;
use JTL\Plugin\Admin\Validation\LegacyPluginValidator;
use JTL\Plugin\Admin\Validation\PluginValidator;
use JTL\Shop;
use JTL\Update\IMigration;
use JTL\Update\Migration;
use JTL\XMLParser;

/**
 * Class Migration20191204125100
 */
class Migration20191204125100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Integrate user backendextension plugin in shop core';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $db              = $this->getDB();
        $cache           = Shop::Container()->getCache();
        $parser          = new XMLParser();
        $legacyValidator = new LegacyPluginValidator($db, $parser);
        $pluginValidator = new PluginValidator($db, $parser);
        $stateChanger    = new StateChanger($db, $cache, $legacyValidator, $pluginValidator);

        $res = $db->getSingleObject(
            "SELECT kPlugin
                  FROM tplugin
                  WHERE cPluginID = 'jtl_backenduser_extension'"
        );
        if ($res !== null) {
            $stateChanger->deactivate((int)$res->kPlugin);
        }

        $this->execute(
            "UPDATE `tadminloginattribut`
               SET cAttribValue = 'N'
               WHERE cName = 'useAvatar'
               AND cAttribValue = 'G'"
        );
        $this->execute("DELETE FROM `tadminloginattribut` WHERE cName = 'useGPlus' OR cName = 'useGravatarEmail'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
