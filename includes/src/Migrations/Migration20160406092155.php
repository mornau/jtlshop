<?php

/**
 * tell_a_friend
 *
 * @author wp
 * @created Wed, 06 Apr 2016 09:21:55 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160406092155
 */
class Migration20160406092155 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'wp';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('DROP TABLE IF EXISTS `tartikelweiterempfehlenhistory`;');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'CREATE TABLE `tartikelweiterempfehlenhistory` (
                  `kArtikelWeiterempfehlenHistory` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `kArtikel` int(10) unsigned NOT NULL,
                  `kSprache` int(10) unsigned NOT NULL,
                  `kKunde` int(10) unsigned NOT NULL,
                  `cName` varchar(255) NOT NULL,
                  `cEmail` varchar(255) NOT NULL,
                  `cIP` varchar(255) NOT NULL,
                  `dErstellt` datetime NOT NULL,
                  PRIMARY KEY (`kArtikelWeiterempfehlenHistory`),
                  KEY `kArtikel` (`kArtikel`,`kSprache`,`cIP`)
                ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }
}
