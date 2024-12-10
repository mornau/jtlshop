<?php

/**
 * Create category-attributes language table
 *
 * @author root
 * @created Mon, 04 Jul 2016 11:02:44 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160704110244
 */
class Migration20160704110244 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE IF NOT EXISTS `tkategorieattributsprache` (
                `kAttribut`                    INT          NOT NULL,
                `kSprache`                     INT          NOT NULL,
                `cName`                        VARCHAR(255) NOT NULL,
                `cWert`                        TEXT         NOT NULL,
                UNIQUE INDEX `kKategorieAttribut_UNIQUE` (`kAttribut`, `kSprache`)) 
                ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );

        $this->execute(
            'ALTER TABLE `tkategorieattribut` 
                ADD COLUMN `nSort`                 INT          NOT NULL DEFAULT 0 AFTER `cWert`,
                ADD COLUMN `bIstFunktionsAttribut` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `nSort`'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'DELETE FROM `tkategorieattribut` WHERE bIstFunktionsAttribut = 0'
        );
        $this->execute(
            'ALTER TABLE `tkategorieattribut` 
                DROP COLUMN `bIstFunktionsAttribut`,
                DROP COLUMN `nSort`'
        );
        $this->execute('DROP TABLE IF EXISTS `tkategorieattributsprache`');
    }
}
