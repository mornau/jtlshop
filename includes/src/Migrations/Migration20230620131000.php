<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230620131000
 */
class Migration20230620131000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add admin sessions table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE IF NOT EXISTS `active_admin_sessions` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `valid` TINYINT(1) NOT NULL DEFAULT 1,
              `sessionID` VARCHAR(255) NOT NULL,
              `userID` INT NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS `active_admin_sessions`');
    }
}
