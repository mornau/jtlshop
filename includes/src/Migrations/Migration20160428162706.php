<?php

/**
 * remove_saferpay
 *
 * @author wp
 * @created Thu, 28 Apr 2016 16:27:06 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160428162706
 */
class Migration20160428162706 extends Migration implements IMigration
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
        $this->execute("UPDATE `tzahlungsart` SET `nActive` = 0, `nNutzbar` = 0 WHERE `cModulId` = 'za_saferpay_jtl'");
        $this->execute(
            "DELETE FROM `tversandartzahlungsart` 
                WHERE `kZahlungsart` IN 
                      (SELECT `kZahlungsart` FROM `tzahlungsart` WHERE `cModulId` = 'za_saferpay_jtl')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE `tzahlungsart` SET `nActive` = 1, `nNutzbar` = 1 WHERE `cModulId` = 'za_saferpay_jtl'");
    }
}
