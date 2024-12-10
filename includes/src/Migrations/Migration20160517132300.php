<?php

/**
 * moves the 404 page into the hidden linkgroup
 *
 * @author ms
 * @created Tue, 17 May 2016 13:23:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160517132300
 */
class Migration20160517132300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE `tlink` SET `kLinkgruppe` = 
              (SELECT `kLinkgruppe` FROM `tlinkgruppe` WHERE `cName` = 'hidden') WHERE `nLinkart`= '29';"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE `tlink` SET `kLinkgruppe`='0' WHERE `nLinkart`= '29';");
    }
}
