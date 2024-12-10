<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200921113900
 */
class Migration20200921113900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add missing configgroups';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'configgroup_' . \CONF_CONSENTMANAGER . '_consentmanager',
            'Cache',
            \CONF_CONSENTMANAGER,
            'Cache',
            null,
            1,
            (object)['cConf' => 'N']
        );
        $this->setConfig(
            'configgroup_' . \CONF_CACHING . '_cache',
            'Consent manager',
            \CONF_CACHING,
            'Consent manager',
            null,
            1,
            (object)['cConf' => 'N']
        );
        $this->setConfig(
            'configgroup_' . \CONF_CRON . '_cron',
            'Cron',
            \CONF_CRON,
            'Cron',
            null,
            1,
            (object)['cConf' => 'N']
        );
        $this->setConfig(
            'configgroup_' . \CONF_FS . '_filesystem',
            'Filesystem',
            \CONF_FS,
            'Filesystem',
            null,
            1,
            (object)['cConf' => 'N']
        );
        $this->setConfig(
            'configgroup_' . \CONF_AUSWAHLASSISTENT . '_selectionwizard',
            'Auswahlassistent',
            \CONF_AUSWAHLASSISTENT,
            'Auswahlassistent',
            null,
            1,
            (object)['cConf' => 'N']
        );
        $this->execute("UPDATE teinstellungenconf SET nSort = 5 WHERE cWertName = 'caching_activated'");
        $this->execute("UPDATE teinstellungenconf SET nSort = 5 WHERE cWertName = 'cron_type'");
        $this->execute("UPDATE teinstellungenconf SET nSort = 10 WHERE cWertName = 'cron_freq'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('configgroup_' . \CONF_CACHING . '_cache');
        $this->removeConfig('configgroup_' . \CONF_CONSENTMANAGER . '_consentmanager');
        $this->removeConfig('configgroup_' . \CONF_CRON . '_cron');
        $this->removeConfig('configgroup_' . \CONF_FS . '_filesystem');
        $this->removeConfig('configgroup_' . \CONF_AUSWAHLASSISTENT . '_selectionwizard');
        $this->execute("UPDATE teinstellungenconf SET nSort = 1 WHERE cWertName = 'caching_activated'");
        $this->execute("UPDATE teinstellungenconf SET nSort = 1 WHERE cWertName = 'cron_type'");
        $this->execute("UPDATE teinstellungenconf SET nSort = 2 WHERE cWertName = 'cron_freq'");
    }
}
