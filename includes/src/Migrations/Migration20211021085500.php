<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20211021085500
 */
class Migration20211021085500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add productdetails content setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'artikeldetails_inhalt_anzeigen',
            'Y',
            \CONF_ARTIKELDETAILS,
            'Inhalt anzeigen',
            'selectbox',
            1475,
            (object)[
                'cBeschreibung' => 'Inhalt in der Beschreibungs-Registerkarte anzeigen.',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('artikeldetails_inhalt_anzeigen');
    }
}
