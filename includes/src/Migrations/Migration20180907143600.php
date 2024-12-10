<?php

/**
 * @author fm
 * @created Fri, 07 Sep 2018 14:36:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180907143600,
 */
class Migration20180907143600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Update cUnique fields';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `twarenkorbperspos`
                CHANGE COLUMN `cUnique` `cUnique` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL'
        );
        $this->execute(
            'ALTER TABLE `twarenkorbpos`
                CHANGE COLUMN `cUnique` `cUnique` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `twarenkorbperspos` CHANGE COLUMN `cUnique` `cUnique` VARCHAR(10) NOT NULL');
        $this->execute('ALTER TABLE `twarenkorbpos` CHANGE COLUMN `cUnique` `cUnique` VARCHAR(10) NOT NULL');
    }
}
