<?php

/**
 * Remove page cache options
 *
 * @author fm
 * @created Wed, 07 Sep 2016 12:11:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160907142800
 */
class Migration20160907142800 extends Migration implements IMigration
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
            "CREATE TABLE `trevisions` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `type` VARCHAR(255) NOT NULL,
              `reference_primary` INT(11) NOT NULL,
              `reference_secondary` INT(11) DEFAULT NULL,
              `content` TEXT NOT NULL DEFAULT '',              
              `author` TEXT NOT NULL DEFAULT '',              
              `custom_table` TEXT NOT NULL DEFAULT '',              
              `custom_primary_key` TEXT NOT NULL DEFAULT '',              
              `timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS `trevisions`');
    }
}
