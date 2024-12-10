<?php

/**
 * Create a new table to hold the emergency-codes for the 2FA.
 *
 * @author cr
 * @created Mon, 06 Mar 2017 13:08:02 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170306130802
 */
class Migration20170306130802 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Create a new table to hold the emergency-codes for the 2FA.';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "CREATE TABLE IF NOT EXISTS `tadmin2facodes` (
                `kAdminlogin` INT(11) NOT NULL DEFAULT 0, 
                `cEmergencyCode` VARCHAR(64) NOT NULL DEFAULT '', 
                KEY `kAdminlogin` (`kAdminlogin`), 
                UNIQUE KEY `cEmergencyCode` (`cEmergencyCode`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE `tadmin2facodes`');
    }
}
