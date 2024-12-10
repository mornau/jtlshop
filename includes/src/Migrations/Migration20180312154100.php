<?php

/**
 * Update tjtllog.nLevel to INT
 *
 * @author fm
 * @created Mon, 12 Mar 2018 15:41:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180312154100
 */
class Migration20180312154100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Update tjtllog.nLevel to INT';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tjtllog` CHANGE COLUMN `nLevel` `nLevel` INT UNSIGNED NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tjtllog` CHANGE COLUMN `nLevel` `nLevel` TINYINT UNSIGNED NOT NULL');
    }
}
