<?php

/**
 * Add language variables for the new pagination
 *
 * @author fm
 * @created Mon, 12 Sep 2016 17:30:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160913123000
 */
class Migration20160913123000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'aj';
    }

    public function getDescription(): string
    {
        return 'Create admin favorite table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "CREATE TABLE `tadminfavs` (
             `kAdminfav` int(10) unsigned NOT NULL AUTO_INCREMENT,
             `kAdminlogin` int(10) unsigned NOT NULL,
             `cTitel` varchar(255) NOT NULL,
             `cUrl` varchar(255) NOT NULL,
             `nSort` int(10) unsigned NOT NULL DEFAULT '0',
             PRIMARY KEY (`kAdminfav`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE `tadminfavs`');
    }
}
