<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210608094115
 */
class Migration20210608094115 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Add characteristic filter setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'merkmalfilter_trefferanzahl_anzeigen',
            'E',
            \CONF_NAVIGATIONSFILTER,
            'Trefferanzahl bei Merkmalfiltern anzeigen',
            'selectbox',
            183,
            (object)[
                'cBeschreibung' => 'Trefferanzahl bei Merkmalfiltern anzeigen?',
                'inputOptions'  => [
                    'N' => 'Trefferanzahl nie anzeigen',
                    'E' => 'Trefferanzahl nur bei Einfachauswahl anzeigen',
                    'Y' => 'Trefferanzahl auch bei möglicher Mehrfachauswahl anzeigen (performancelastig)'
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('merkmalfilter_trefferanzahl_anzeigen');
    }
}
