<?php

/**
 * adds options for short description
 *
 * @author ms
 * @created Fri, 07 Oct 2016 14:31:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161007143100
 */
class Migration20161007143100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'artikeldetails_kurzbeschreibung_anzeigen',
            'Y',
            \CONF_ARTIKELDETAILS,
            'Kurzbeschreibung anzeigen',
            'selectbox',
            365,
            (object)[
                'cBeschreibung' => 'Soll die Kurzbeschreibung des Artikels auf der Detailseite angezeigt werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ]
        );
        $this->setConfig(
            'artikeluebersicht_kurzbeschreibung_anzeigen',
            'N',
            \CONF_ARTIKELUEBERSICHT,
            'Kurzbeschreibung anzeigen',
            'selectbox',
            315,
            (object)[
                'cBeschreibung' => 'Soll die Kurzbeschreibung des Artikels auf &Uuml;bersichtsseiten angezeigt werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('artikeldetails_kurzbeschreibung_anzeigen');
        $this->removeConfig('artikeluebersicht_kurzbeschreibung_anzeigen');
    }
}
