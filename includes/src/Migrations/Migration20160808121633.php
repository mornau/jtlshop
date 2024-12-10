<?php

/**
 * delete_serbia_and_montenegro_from_tland
 *
 * @author msc
 * @created Mon, 08 Aug 2016 12:16:33 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160808121633
 */
class Migration20160808121633 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("DELETE FROM `tland` WHERE `cISO` = 'YU'");
        $this->execute("UPDATE `tland` SET `cEnglisch` = 'Serbia' WHERE `cISO` = 'RS'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO `tland` (`cISO`, `cDeutsch`, `cEnglisch`, `nEU`, `cKontinent`)
                VALUES('YU', 'Serbien und Montenegro', 'Serbia and Montenegro', 0, 'Europa')"
        );
        $this->execute("UPDATE `tland` SET `cEnglisch` = 'Serbien' WHERE `cISO` = 'RS'");
    }
}
