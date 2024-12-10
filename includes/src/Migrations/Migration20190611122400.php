<?php

/**
 * remove nglobal from tmerkmal
 *
 * @author mh
 * @created Tue, 11 June 2019 12:24:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190611122400
 */
class Migration20190611122400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove nGlobal from tmerkmal';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tmerkmal` DROP COLUMN `nGlobal`');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tmerkmal` ADD COLUMN `nGlobal` TINYINT(4) DEFAULT 0');
    }
}
