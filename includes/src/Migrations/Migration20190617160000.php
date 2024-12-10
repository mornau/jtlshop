<?php

/**
 * Separate manufacturer conf
 *
 * @author mh
 * @created Wed, 17 June 2019 16:00:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190617160000
 */
class Migration20190617160000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Separate manufacturer conf';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE `teinstellungenconf` SET nSort=112 WHERE cWertName='search_special_filter_type'");
        $this->execute(
            "UPDATE `teinstellungenconf`
                SET nSort = 110
                WHERE cWertName = 'allgemein_suchspecialfilter_benutzen'"
        );
        $this->execute(
            "UPDATE `teinstellungenconf`
                SET nSort = 125
                WHERE cWertName = 'allgemein_herstellerfilter_benutzen'"
        );
        $this->execute("UPDATE `teinstellungenconf` SET nSort=126 WHERE cWertName='manufacturer_filter_type'");

        $this->setConfig(
            'configgroup_110_manufacturer_filter',
            'Herstellerfilter',
            \CONF_NAVIGATIONSFILTER,
            'Herstellerfilter',
            null,
            120,
            (object)['cConf' => 'N'],
            true
        );
        $this->setConfig(
            'hersteller_anzeigen_als',
            'T',
            \CONF_NAVIGATIONSFILTER,
            'Hersteller anzeigen als',
            'selectbox',
            127,
            (object)[
                'cBeschreibung' => 'Hier legen Sie fest, wie die Hersteller angezeigt werden sollen.',
                'inputOptions'  => [
                    'T'  => 'Text',
                    'BT' => 'Bild und Text',
                    'B'  => 'Bild'
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE `teinstellungenconf` SET nSort = 141 WHERE cWertName = 'search_special_filter_type'");
        $this->execute(
            "UPDATE `teinstellungenconf`
                SET nSort = 140
                WHERE cWertName = 'allgemein_suchspecialfilter_benutzen'"
        );
        $this->execute(
            "UPDATE `teinstellungenconf`
                SET nSort = 120
                WHERE cWertName = 'allgemein_herstellerfilter_benutzen'"
        );
        $this->execute("UPDATE `teinstellungenconf` SET nSort = 121 WHERE cWertName = 'manufacturer_filter_type'");

        $this->removeConfig('configgroup_110_manufacturer_filter');
        $this->removeConfig('hersteller_anzeigen_als');
    }
}
