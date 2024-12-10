<?php

/**
 * add new special page type for compare list
 *
 * @author fm
 * @created Thu, 15 Sep 2016 17:52:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160915175200
 */
class Migration20160915175200 extends Migration implements IMigration
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
            "INSERT INTO `tspezialseite`
                (`kPlugin`, `cName`, `cDateiname`, `nLinkart`, `nSort`)
                 VALUES ('0', 'Vergleichsliste', 'vergleichsliste.php', '35', '35')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tspezialseite` WHERE `nLinkart` = '35'");
    }
}
