<?php

/**
 * add new object cache method
 *
 * @author fm
 * @created Fri, 21 Oct 2016 18:00:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161021180000
 */
class Migration20161021180000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "INSERT INTO teinstellungenconfwerte (kEinstellungenConf, cName, cWert, nSort)
                VALUES (1551, 'Dateien (erweitert)', 'advancedfile', 9)"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "DELETE FROM teinstellungenconfwerte
                WHERE kEinstellungenConf = 1551 AND cWert = 'advancedfile'"
        );
    }
}
