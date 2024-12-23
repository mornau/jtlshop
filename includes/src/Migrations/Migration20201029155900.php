<?php

/**
 * Drop table for price range
 *
 * @author fp
 * @created Wed, 04 Apr 2018 10:01:49 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180404100149
 */
class Migration20201029155900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Drop the table for price range';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'DROP TABLE `tpricerange`'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'CREATE TABLE IF NOT EXISTS `tpricerange` (
                `kPriceRange`     INT(11)    UNSIGNED NOT NULL AUTO_INCREMENT,
                `kArtikel`        INT(11)    UNSIGNED NOT NULL,
                `kKundengruppe`   INT(11)    UNSIGNED NOT NULL DEFAULT 0,
                `kKunde`          INT(11)    UNSIGNED NOT NULL DEFAULT 0,
                `nRangeType`      TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
                `fVKNettoMin`     DOUBLE              NOT NULL DEFAULT 0,
                `fVKNettoMax`     DOUBLE              NOT NULL DEFAULT 0,
                `nLagerAnzahlMax` DOUBLE                  NULL,
                `dStart`          DATE                    NULL,
                `dEnde`           DATE                    NULL,
                PRIMARY KEY (`kPriceRange`),
                UNIQUE INDEX `tpricerange_uq` (`kArtikel` ASC, `kKundengruppe` ASC, `kKunde` ASC, `nRangeType` ASC)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }
}
