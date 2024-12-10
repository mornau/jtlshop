<?php

/**
 * remove-shopinfo-menu-point
 *
 * @author mschop
 * @created Thu, 01 Mar 2018 13:52:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180301135200
 */
class Migration20180301135200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mschop';
    }

    public function getDescription(): string
    {
        return 'Remove shopinfo menu item';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("DELETE FROM tadminmenu WHERE cLinkname = 'Shopinfo (elm@ar)'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO tadminmenu (kAdminmenueGruppe, cModulId, cLinkname, cURL, cRecht, nSort)
          VALUES (12, 'core_jtl', 'Shopinfo (elm@ar)', 'shopinfoexport.php', 'EXPORT_SHOPINFO_VIEW', 40)"
        );
    }
}
