<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Backend\Permissions;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240607110600
 */
class Migration20240607110600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add report permission and table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE IF NOT EXISTS reports (
                id           INT NOT NULL AUTO_INCREMENT,
                remoteIP    VARCHAR(255) DEFAULT NULL,
                file    VARCHAR(255) NOT NULL,
                hash    VARCHAR(255) NOT NULL,
                created    DATETIME NOT NULL,
                visited    DATETIME DEFAULT NULL,
                validUntil    DATETIME DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
        );
        $this->execute(
            "INSERT INTO `tadminrecht` (`cRecht`, `cBeschreibung`)
            VALUES ('" . Permissions::REPORT_VIEW . "', 'Report')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS reports');
        $this->execute("DELETE FROM `tadminrecht` WHERE `cRecht` = '" . Permissions::REPORT_VIEW . "'");
    }
}
