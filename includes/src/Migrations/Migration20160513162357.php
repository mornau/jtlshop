<?php

/**
 * added_option_for_dimension_of_articles
 *
 * @author msc
 * @created Fri, 13 May 2016 16:23:57 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160513162357
 */
class Migration20160513162357 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "INSERT INTO `teinstellungenconf` (`kEinstellungenConf`,`kEinstellungenSektion`,
                                  `cName`,`cBeschreibung`,`cWertName`,`cInputTyp`,`cModulId`,
                                  `nSort`,`nStandardAnzeigen`,`nModul`,`cConf`)
                VALUES (1651,5,'Abmessungen anzeigen?', 'Maße des Artikels in LxBxH',
                        'artikeldetails_abmessungen_anzeigen','selectbox',NULL,1490,1,0,'Y')"
        );
        $this->execute(
            "INSERT INTO `teinstellungen` (`kEinstellungenSektion`,`cName`,`cWert`)
              VALUES (5,'artikeldetails_abmessungen_anzeigen','N')"
        );
        $this->execute(
            "INSERT INTO `teinstellungenconfwerte` (`kEinstellungenConf`,`cName`,`cWert`,`nSort`)
                VALUES (1651,'Nein','N',1)"
        );
        $this->execute(
            "INSERT INTO `teinstellungenconfwerte` (`kEinstellungenConf`,`cName`,`cWert`,`nSort`)
              VALUES (1651,'Ja','Y',2)"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('artikeldetails_abmessungen_anzeigen');
    }
}
