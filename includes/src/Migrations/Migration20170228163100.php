<?php

/**
 * sets nofollow for special pages
 *
 * @author ms
 * @created Tue, 28 Feb 2017 16:31:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170228163100
 */
class Migration20170228163100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Set nofollow for special pages';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE `tlink` SET `cNoFollow` = 'Y' WHERE `nLinkart`= '11' OR `nLinkart`= '12' OR `nLinkart`= '24';"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE `tlink` SET `cNoFollow` = 'N' WHERE `nLinkart`= '11' OR `nLinkart`= '12' OR `nLinkart`= '24';"
        );
    }
}
