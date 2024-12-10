<?php

/**
 * @author ms
 * @created Mon, 12 Sep 2016 15:53:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160912155300
 */
class Migration20160912155300 extends Migration implements IMigration
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
        $this->execute('ALTER TABLE `twarenkorbperspos` ADD COLUMN `nPosTyp` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `twarenkorbperspos` DROP COLUMN `nPosTyp`');
    }
}
