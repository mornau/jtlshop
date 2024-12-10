<?php

/**
 * Adds backend links for premium plugins
 *
 * @author fm
 * @created Thu, 30 Jun 2016 12:15:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160630121500
 */
class Migration20160630121500 extends Migration implements IMigration
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
            "INSERT INTO `tadminmenu` (`kAdminmenueGruppe`, `cModulId`, `cLinkname`, `cURL`, `cRecht`, `nSort`)
                VALUES ('18', 'core_jtl', 'Amazon Payments',
                        'premiumplugin.php?plugin_id=s360_amazon_lpa_shop4', 'PLUGIN_ADMIN_VIEW', '315')"
        );
        $this->execute(
            "INSERT INTO `tadminmenu` (`kAdminmenueGruppe`, `cModulId`, `cLinkname`, `cURL`, `cRecht`, `nSort`)
              VALUES ('16', 'core_jtl', 'TrustedShops Trustbadge Reviews',
                      'premiumplugin.php?plugin_id=agws_ts_features', 'PLUGIN_ADMIN_VIEW', '315')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tadminmenu` WHERE `nSort` = 315 AND cRecht = 'PLUGIN_ADMIN_VIEW'");
    }
}
