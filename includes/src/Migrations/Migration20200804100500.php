<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200804100500
 */
class Migration20200804100500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add review sort option';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'bewertung_sortierung',
            0,
            \CONF_BEWERTUNG,
            'Standard-Sortierung',
            'selectbox',
            125,
            (object)[
                'cBeschreibung' => 'Hier legen Sie fest, wie Bewertungen standardmäßig sortiert werden.',
                'inputOptions'  => [
                    0 => 'Datum aufsteigend',
                    1 => 'Datum absteigend',
                    2 => 'Bewertung aufsteigend',
                    3 => 'Bewertung absteigend',
                    4 => 'Hilfreich aufsteigend',
                    5 => 'Hilfreich absteigend',
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('bewertung_sortierung');
    }
}
