<?php

/**
 * create store table
 *
 * @author aj
 * @created Mon, 17 Nov 2018 13:33:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181117133300
 */
class Migration20181117133300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'aj';
    }

    public function getDescription(): string
    {
        return 'Create store table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE `tstoreauth` (
                `auth_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
                `access_token` mediumtext COLLATE utf8mb4_unicode_ci,
                `created_at` datetime NOT NULL
                ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE `tstoreauth`');
    }
}
