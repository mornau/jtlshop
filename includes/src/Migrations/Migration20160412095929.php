<?php

/**
 * remove_clickandbuy
 *
 * @author wp
 * @created Tue, 12 Apr 2016 09:59:29 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160412095929
 */
class Migration20160412095929 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'wp';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE `tzahlungsart` SET `nActive` = 0 WHERE `cModulId` = 'za_clickandbuy_jtl'");
        $this->execute(
            "DELETE FROM `tversandartzahlungsart` 
            WHERE `kZahlungsart` IN 
                  (SELECT `kZahlungsart` FROM `tzahlungsart` WHERE `cModulId` = 'za_clickandbuy_jtl')"
        );
        $this->execute('DELETE FROM `tadminmenu` WHERE `kAdminmenu` = 52');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE `tzahlungsart` SET `nActive` = 1 WHERE `cModulId` = 'za_clickandbuy_jtl'");
        $this->execute(
            "INSERT INTO `tadminmenu` 
              (`kAdminmenu`, `kAdminmenueGruppe`, `cModulId`, `cLinkname`, `cURL`, `cRecht`, `nSort`) 
              VALUES (52, 18, 'core_jtl', 'ClickandBuy', 'clickandbuy.php', 'ORDER_CLICKANDBUY_VIEW', 150)"
        );
    }
}
