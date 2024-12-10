<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200407153000
 */
class Migration20200407153000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add license data table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE IF NOT EXISTS `licenses` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              `data` mediumtext NOT NULL,
              `returnCode` int(11) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
        );
        $this->execute(
            "INSERT INTO `tadminrecht` (`cRecht`, `cBeschreibung`)
            VALUES ('LICENSE_MANAGER', 'License Manager')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS licenses');
        $this->execute("DELETE FROM `tadminrecht` WHERE `cRecht` = 'LICENSE_MANAGER'");
    }
}
