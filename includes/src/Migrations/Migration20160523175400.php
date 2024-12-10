<?php

/**
 * @author fm
 * @created Mon, 23 May 2016 17:54:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160523175400
 */
class Migration20160523175400 extends Migration implements IMigration
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
        $this->execute('ALTER TABLE `texportformat` ADD COLUMN `nUseCache` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `texportformat` DROP COLUMN `nUseCache`');
    }
}
