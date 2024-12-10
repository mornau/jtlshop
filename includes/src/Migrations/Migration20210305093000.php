<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210305093000
 */
class Migration20210305093000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add manufacturer box count settings';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'configgroup_' . \CONF_BOXEN . '_box_manufacturers',
            'Hersteller',
            \CONF_BOXEN,
            'Hersteller',
            null,
            150,
            (object)['cConf' => 'N']
        );
        $this->setConfig(
            'box_hersteller_anzahl_anzeige',
            '20',
            \CONF_BOXEN,
            'Hersteller Anzahl',
            'number',
            160
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('configgroup_' . \CONF_BOXEN . '_box_manufacturers');
        $this->removeConfig('box_hersteller_anzahl_anzeige');
    }
}
