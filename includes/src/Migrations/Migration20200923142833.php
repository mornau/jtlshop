<?php

/**
 * Create notifications ignore table.
 *
 * @author fp
 * @created Wed, 23 Sep 2020 14:28:33 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200923142833
 */
class Migration20200923142833 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create notifications ignore table.';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE tnotificationsignore (
            id                int         NOT NULL AUTO_INCREMENT,
            user_id           int         NOT NULL,
            notification_hash varchar(40) NOT NULL,
            created           datetime,
            PRIMARY KEY (id),
            UNIQUE KEY idx_notificationignore_hash_uq (user_id, notification_hash)
        ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS tnotificationsignore');
    }
}
