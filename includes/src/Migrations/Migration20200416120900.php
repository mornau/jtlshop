<?php

/**
 * Remove sorting by availability
 *
 * @author mh
 * @created Thu, 16 Apr 2020 12:09:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200416120900
 */
class Migration20200416120900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove sorting by availability';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('suche_sortierprio_lagerbestand');

        $this->removeLocalization('sortAvailability', 'global');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'suche_sortierprio_lagerbestand',
            '6',
            \CONF_ARTIKELUEBERSICHT,
            'Priorität der Suchtreffersortierung: Verfügbarkeit',
            'number',
            240,
            (object)[
                'cBeschreibung' => '0 - diese Sortiermöglichkeit wird nicht angeboten. Sortierung nach Lagerbestand'
            ]
        );
        $this->setLocalization('ger', 'global', 'sortAvailability', 'Lagerbestand');
        $this->setLocalization('eng', 'global', 'sortAvailability', 'Stock level');
    }
}
