<?php

/**
 * New Table for order attributes
 *
 * @author fp
 * @created Wed, 10 May 2017 09:41:18 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170510094118
 */
class Migration20170510094118 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'New Table for order attributes';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE tbestellattribut (
                kBestellattribut INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                kBestellung      INT(10) UNSIGNED NOT NULL,
                cName            VARCHAR(255)     NOT NULL,
                cValue           TEXT                 NULL,
                PRIMARY KEY (kBestellattribut),
                UNIQUE KEY idx_kBestellung_cName_uq (kBestellung, cName)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE tbestellattribut');
    }
}
