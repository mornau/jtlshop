<?php

/**
 * Configuration for price range
 *
 * @author fp
 * @created Mon, 16 Apr 2018 10:38:17 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180416103817
 */
class Migration20180416103817 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Configuration for price range';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'articleoverview_pricerange_width',
            '150',
            \CONF_ARTIKELUEBERSICHT,
            'Max. Abweichung (%) für Preis-Range Anzeige',
            'number',
            372,
            (object)[
                'cBeschreibung' => 'Überschreitet der Max. Preis den Min. Preis um die angegebenen Prozent, ' .
                    'dann wird stattdessen nur ein "ab" angezeigt.',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('articleoverview_pricerange_width');
    }
}
