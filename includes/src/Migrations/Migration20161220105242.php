<?php

/**
 * Refactor category nested set level
 *
 * @author fp
 * @created Tue, 20 Dec 2016 10:52:42 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161220105242
 */
class Migration20161220105242 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Refactor category nested set level';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE tkategorie
                ADD COLUMN nLevel int(10) unsigned NOT NULL DEFAULT 0 AFTER rght'
        );

        $this->execute(
            'UPDATE tkategorie
                SET nLevel = (
                    SELECT nLevel 
                    FROM tkategorielevel 
                    WHERE tkategorielevel.kKategorie = tkategorie.kKategorie)'
        );

        $this->execute(
            'DROP TABLE tkategorielevel'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'CREATE TABLE tkategorielevel (
                kKategorieLevel     int(10) unsigned NOT NULL AUTO_INCREMENT,
                kKategorie          int(10) unsigned NOT NULL,
                nLevel              int(10) unsigned NOT NULL,
                PRIMARY KEY (kKategorieLevel),
                UNIQUE KEY kKategorie (kKategorie)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );

        $this->execute(
            'INSERT INTO tkategorielevel (kKategorie, nLevel)
                SELECT kKategorie, nLevel FROM tkategorie'
        );

        $this->execute(
            'ALTER TABLE tkategorie
                DROP COLUMN nLevel'
        );
    }
}
