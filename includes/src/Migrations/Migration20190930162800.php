<?php

/**
 * @author mh
 * @created Mo, 30 September 2019 16:28:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190930162800
 */
class Migration20190930162800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove financial proposal config';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('artikeluebersicht_finanzierung_anzeigen');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'artikeluebersicht_finanzierung_anzeigen',
            'N',
            \CONF_ARTIKELUEBERSICHT,
            'Finanzierungsvorschlag anzeigen',
            'selectbox',
            480,
            (object)[
                'cBeschreibung' => 'Wollen Sie das in der Artikelübersicht bei jedem Artikel ein' .
                    'Finanzierungsvorschlag angezeigt wird?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ]
        );
    }
}
