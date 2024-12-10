<?php

/**
 * add_table_passwordreset
 *
 * @author mschop
 * @created Fri, 02 Feb 2018 14:52:24 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180202145224
 */
class Migration20180202145224 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mschop';
    }

    public function getDescription(): string
    {
        return 'Add Table tpasswordreset';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE tpasswordreset(
            kKunde INT PRIMARY KEY ,
            cKey VARCHAR(255) UNIQUE,
            dExpires DATETIME
          ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
          CREATE INDEX tpasswordreset_cKey ON tpasswordreset(cKey);
          ALTER TABLE tkunde DROP COLUMN cResetPasswordHash;
        '
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE tpasswordreset');
        $this->execute('ALTER TABLE tkunde ADD COLUMN cResetPasswordHash VARCHAR(255)');
    }
}
