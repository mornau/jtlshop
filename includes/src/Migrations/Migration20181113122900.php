<?php

/**
 * @author fm
 * @created Tue, 13 Nov 2018 11:29:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181113122900
 */
class Migration20181113122900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Changes for new extensions';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tplugin` CHANGE COLUMN `nVersion` `nVersion` VARCHAR(255) NOT NULL');
        $this->execute(
            "ALTER TABLE `tboxvorlage` 
            CHANGE COLUMN `eTyp` `eTyp` ENUM('tpl', 'text', 'link', 'plugin', 'catbox', 'extension')"
        );
        $this->execute('ALTER TABLE `tplugin` ADD COLUMN `bExtension` TINYINT(1) NOT NULL DEFAULT 0');
        $this->execute(
            'CREATE TABLE IF NOT EXISTS tpluginmigration 
            (
                kMigration bigint(14) NOT NULL, 
                nVersion int(3) NOT NULL, 
                pluginID varchar(255) NOT NULL, 
                dExecuted datetime NOT NULL,
                PRIMARY KEY (kMigration)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tplugin` CHANGE COLUMN `nVersion` `nVersion` INT NOT NULL');
        $this->execute(
            "ALTER TABLE `tboxvorlage` 
            CHANGE COLUMN `eTyp` `eTyp` ENUM('tpl', 'text', 'link', 'plugin', 'catbox')"
        );
        $this->execute('ALTER TABLE `tplugin` DROP COLUMN `bExtension`');
        $this->execute('DROP TABLE `tpluginmigration`');
    }
}
