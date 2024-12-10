<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201117143600
 */
class Migration20201117143600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Backend improvements';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE `teinstellungenconf` SET nSort=50 WHERE cWertName='configgroup_110_general'");
        $this->execute("UPDATE `teinstellungenconf` SET nSort=53 WHERE cWertName='allgemein_weiterleitung'");
        $this->execute(
            "UPDATE `teinstellungenconf`
                SET nSort = 55
                WHERE cWertName='allgemein_suchspecialfilter_benutzen'"
        );
        $this->execute("UPDATE `teinstellungenconf` SET nSort=57 WHERE cWertName='search_special_filter_type'");
        $this->execute("UPDATE `teinstellungenconf` SET nSort=60 WHERE cWertName='configgroup_110_current_category'");
        $this->execute("UPDATE `teinstellungenconf` SET nSort=63 WHERE cWertName='kategorie_bild_anzeigen'");
        $this->execute("UPDATE `teinstellungenconf` SET nSort=65 WHERE cWertName='kategorie_beschreibung_anzeigen'");
        $this->execute("UPDATE `teinstellungenconf` SET nSort=67 WHERE cWertName='artikeluebersicht_bild_anzeigen'");
        $this->execute("UPDATE `teinstellungenconf` SET nSort=70 WHERE cWertName='unterkategorien_lvl2_anzeigen'");
        $this->execute(
            "UPDATE `teinstellungenconf`
                SET nSort = 75
                WHERE cWertName='unterkategorien_beschreibung_anzeigen'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE `teinstellungenconf` SET nSort=100 WHERE cWertName='configgroup_110_general'");
        $this->execute("UPDATE `teinstellungenconf` SET nSort=105 WHERE cWertName='allgemein_weiterleitung'");
        $this->execute(
            "UPDATE `teinstellungenconf`
                SET nSort = 110
                WHERE cWertName = 'allgemein_suchspecialfilter_benutzen'"
        );
        $this->execute(
            "UPDATE `teinstellungenconf`
                SET nSort = 112
                WHERE cWertName = 'allgemein_suchspecialfilter_benutzen'"
        );
        $this->execute("UPDATE `teinstellungenconf` SET nSort=260 WHERE cWertName='configgroup_110_current_category'");
        $this->execute("UPDATE `teinstellungenconf` SET nSort=270 WHERE cWertName='kategorie_bild_anzeigen'");
        $this->execute("UPDATE `teinstellungenconf` SET nSort=280 WHERE cWertName='kategorie_beschreibung_anzeigen'");
        $this->execute("UPDATE `teinstellungenconf` SET nSort=320 WHERE cWertName='artikeluebersicht_bild_anzeigen'");
        $this->execute("UPDATE `teinstellungenconf` SET nSort=325 WHERE cWertName='unterkategorien_lvl2_anzeigen'");
        $this->execute(
            "UPDATE `teinstellungenconf`
                SET nSort = 331
                WHERE cWertName = 'unterkategorien_beschreibung_anzeigen'"
        );
    }
}
