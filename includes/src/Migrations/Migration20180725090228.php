<?php

/**
 * new table tgratisgeschenk
 *
 * @author mh
 * @created Wed, 25 Jul 2018 09:02:28 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180725090228
 */
class Migration20180725090228 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'New table tgratisgeschenk';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE IF NOT EXISTS `tgratisgeschenk` (
                `kGratisGeschenk` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `kArtikel`        INT(10) UNSIGNED NOT NULL,
                `kWarenkorb`      INT(10) UNSIGNED NOT NULL,
                `nAnzahl`         INT(10) UNSIGNED NOT NULL,
                 PRIMARY KEY (`kGratisGeschenk`),        
                 INDEX `kWarenkorb` (`kWarenkorb`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE `tgratisgeschenk`');
    }
}
