<?php

/**
 * Add boolean mode for fulltext search
 *
 * @author fp
 * @created Wed, 14 Mar 2018 13:37:43 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180314133743
 */
class Migration20180314133743 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Add boolean mode for fulltext search';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "INSERT INTO teinstellungenconfwerte (
                SELECT teinstellungenconf.kEinstellungenConf, 'Volltextsuche (Boolean Mode)', 'B', 3
                FROM teinstellungenconf
                WHERE teinstellungenconf.cWertName = 'suche_fulltext'
            )"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "DELETE teinstellungenconfwerte 
                FROM teinstellungenconfwerte 
                INNER JOIN teinstellungenconf 
                    ON teinstellungenconf.kEinstellungenConf = teinstellungenconfwerte.kEinstellungenConf
                WHERE teinstellungenconf.cWertName = 'suche_fulltext'
                    AND teinstellungenconfwerte.cWert = 'B'"
        );
    }
}
