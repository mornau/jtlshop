<?php

/**
 * remove yatego export from admin menu
 *
 * @author mh
 * @created Wed, 13 Jun 2018 15:13:22 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180613151322
 */
class Migration20180613151322 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove Yatego Export from admin menu';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("DELETE FROM tadminmenu WHERE cURL = 'yatego.export.php'");
        $this->execute("DELETE FROM tadminrecht WHERE cRecht = 'EXPORT_YATEGO_VIEW'");
        $this->execute("DELETE FROM tadminrechtegruppe WHERE cRecht = 'EXPORT_YATEGO_VIEW'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO `tadminmenu` 
            (`kAdminmenu`, `kAdminmenueGruppe`, `cModulId`, `cLinkname`, `cURL`, `cRecht`, `nSort`) 
            VALUES (46, 12, 'core_jtl', 'Yatego Export', 'yatego.export.php', 'EXPORT_YATEGO_VIEW', 70)"
        );
        $this->execute(
            "INSERT INTO `tadminrecht` 
            (`cRecht`, `cBeschreibung`, `kAdminrechtemodul`) 
            VALUES ('EXPORT_YATEGO_VIEW', 'Yatego Export', 7)"
        );
    }
}
