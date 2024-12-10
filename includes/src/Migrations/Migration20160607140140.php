<?php

/**
 * create image history table
 *
 * @author aj
 * @created Tue, 07 Jun 2016 14:01:40 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160607140140
 */
class Migration20160607140140 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'aj';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE IF NOT EXISTS `tartikelpicthistory` (
                 `kArtikel` int(10) unsigned NOT NULL,
                 `cPfad` varchar(255) NOT NULL,
                 `nNr` tinyint(3) unsigned NOT NULL DEFAULT \'1\',
                  UNIQUE KEY `UNIQUE` (`kArtikel`,`nNr`,`cPfad`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
        $this->execute(
            'REPLACE INTO `tartikelpicthistory` 
              (SELECT `kArtikel`, `cPfad`, `nNr` FROM `tartikelpict` WHERE `kBild` = 0)'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS `tartikelpicthistory`');
    }
}
