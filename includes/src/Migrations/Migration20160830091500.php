<?php

/**
 * add new link type for wishlist
 *
 * @author fm
 * @created Tue, 30 Oct 2016 09:15:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160830091500
 */
class Migration20160830091500 extends Migration implements IMigration
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
            "INSERT INTO `tspezialseite` (`kPlugin`, `cName`, `cDateiname`, `nLinkart`, `nSort`)
            VALUES ('0', 'Wunschliste', 'wunschliste.php', '34', '34')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tspezialseite` WHERE `nLinkart` = '34'");
    }
}
