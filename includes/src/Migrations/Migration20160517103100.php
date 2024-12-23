<?php

/**
 * add new link types
 *
 * @author fm
 * @created Mon, 17 May 2016 10:31:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160517103100
 */
class Migration20160517103100 extends Migration implements IMigration
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
              VALUES ('0', 'Bestellvorgang', 'bestellvorgang.php', '32', '32')"
        );
        $this->execute(
            "INSERT INTO `tspezialseite`
              (`kPlugin`, `cName`, `cDateiname`, `nLinkart`, `nSort`)
              VALUES ('0', 'Bestellabschluss', 'bestellabschluss.php', '33', '33')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tspezialseite` WHERE `nLinkart` = '32' OR `nLinkart` = '33'");
    }
}
