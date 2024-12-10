<?php

/**
 * new backend setting for product filtering
 *
 * @author tnt
 * @created Thu, 30 Nov 2023 13:20:11 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240320124941
 */
class Migration20231130132011 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'tnt';
    }

    public function getDescription(): string
    {
        return 'new backend setting for product filtering';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            configName          : 'merkmal_label_anzeigen',
            configValue         : 'Y',
            configSectionID     : CONF_NAVIGATIONSFILTER,
            externalName        : 'Merkmal-Name im aktiven Filter-Label anzeigen',
            inputType           : 'selectbox',
            sort                : 184,
            additionalProperties: (object)[
                'cBeschreibung' => 'Hier legen Sie fest, ob der Merkmal-Name zusätzlich zum Merkmal-Wert im aktiven'
                    . 'Filter-Label angezeigt werden soll.',
                'inputOptions' => [
                    'N' => 'Merkmal-Name anzeigen',
                    'Y' => 'Merkmal-Name nicht anzeigen',
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('merkmal_label_anzeigen');
    }
}
