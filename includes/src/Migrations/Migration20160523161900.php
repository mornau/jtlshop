<?php

/**
 * active status for link sites
 *
 * @author ms
 * @created Mon, 23 May 2016 16:19:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160523161900
 */
class Migration20160523161900 extends Migration implements IMigration
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
        $this->execute('ALTER TABLE `tlink` ADD COLUMN `bIsActive` TINYINT(1) NOT NULL DEFAULT 1;');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tlink` DROP COLUMN `bIsActive`;');
    }
}
