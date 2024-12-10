<?php

/**
 * removed keywording admin menu entry
 *
 * @author fm
 * @created Wed, 16 May 2018 13:13:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180516121200
 */
class Migration20180516121200 extends Migration implements IMigration
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
        $this->execute("DELETE FROM tadminmenu WHERE cURL = 'keywording.php'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO `tadminmenu` 
            (`kAdminmenu`, `kAdminmenueGruppe`, `cModulId`, `cLinkname`, `cURL`, `cRecht`, `nSort`) 
            VALUES (8,7,'core_jtl','Meta-Keywords Blacklist',
                    'keywording.php','SETTINGS_META_KEYWORD_BLACKLIST_VIEW', 20)"
        );
    }
}
