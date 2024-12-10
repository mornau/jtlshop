<?php

/**
 * matrix-settings
 *
 * @author dh
 * @created Fri, 05 Feb 2016 10:53:22 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160205105322
 */
class Migration20160205105322 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dh';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "INSERT INTO `teinstellungenconfwerte` (kEinstellungenConf, cName, cWert, nSort) 
                VALUES (1330, 'Liste (nur bei Varkombis möglich)', 'L', 3);"
        );
        $this->execute(
            "UPDATE `teinstellungenconfwerte`
                SET cName = 'Hochformat (nur bei 1 Variation möglich)'
                WHERE kEinstellungenConf = 1330 AND cWert = 'H';"
        );
        $this->execute(
            "UPDATE `teinstellungenconfwerte`
                SET cName = 'Querformat (nur bei 1 Variation möglich)'
                WHERE kEinstellungenConf = 1330 AND cWert = 'Q';"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `teinstellungenconfwerte` WHERE kEinstellungenConf=1330 AND cWert='L';");
        $this->execute(
            "UPDATE `teinstellungenconfwerte`
                SET cName = 'Hochformat'
                WHERE kEinstellungenConf = 1330 AND cWert = 'H';"
        );
        $this->execute(
            "UPDATE `teinstellungenconfwerte`
                SET cName = 'Querformat' WHERE kEinstellungenConf = 1330 AND cWert = 'Q';"
        );
    }
}
