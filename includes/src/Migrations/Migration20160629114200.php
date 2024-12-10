<?php

/**
 * Add plugin hook priority
 *
 * @author fm
 * @created Wed, 29 Jun 2016 11:42:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160629114200
 */
class Migration20160629114200 extends Migration implements IMigration
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
        $this->execute('ALTER TABLE `tpluginhook` ADD COLUMN `nPriority` INT(10) NULL DEFAULT 5');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tpluginhook` DROP COLUMN `nPriority`');
    }
}
