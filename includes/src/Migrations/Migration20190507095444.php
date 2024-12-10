<?php

/**
 * remove_product_tags
 *
 * @author mh
 * @created Tue, 07 May 2019 09:54:30 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190507095444
 */
class Migration20190507095444 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove tag tables';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('DROP TABLE IF EXISTS `ttag`');
        $this->execute('DROP TABLE IF EXISTS `ttagartikel`');
        $this->execute('DROP TABLE IF EXISTS `ttagkunde`');
        $this->execute('DROP TABLE IF EXISTS `ttagmapping`');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'CREATE TABLE `ttag` (
              `kTag`     INT(10)      UNSIGNED NOT NULL AUTO_INCREMENT,
              `kSprache` TINYINT(4)   UNSIGNED NOT NULL,
              `cName`    VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `cSeo`     VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `nAktiv`   TINYINT(1)   NOT NULL,
              PRIMARY KEY (`kTag`),
              KEY `cSeo` (`cSeo`),
              KEY `kSprache` (`kSprache`,`cName`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );

        $this->execute(
            'CREATE TABLE `ttagartikel` (
              `kTag`           INT(10) UNSIGNED NOT NULL,
              `kArtikel`       INT(10) UNSIGNED NOT NULL,
              `nAnzahlTagging` INT(10) UNSIGNED NOT NULL,
              PRIMARY KEY (`kArtikel`,`kTag`),
              KEY `kTag` (`kTag`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );

        $this->execute(
            'CREATE TABLE `ttagkunde` (
              `kTagKunde` INT(10)      UNSIGNED NOT NULL AUTO_INCREMENT,
              `kTag`      INT(10)      UNSIGNED NOT NULL,
              `kKunde`    INT(10)      UNSIGNED NOT NULL,
              `cIP`       VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `dZeit`     DATETIME     NOT NULL,
              PRIMARY KEY (`kTagKunde`),
              KEY `kKunde` (`kKunde`),
              KEY `cIP` (`cIP`),
              KEY `dZeit` (`dZeit`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );

        $this->execute(
            'CREATE TABLE `ttagmapping` (
              `kTagMapping` INT(10)      UNSIGNED NOT NULL AUTO_INCREMENT,
              `kSprache`    TINYINT(4)   UNSIGNED NOT NULL,
              `cName`       VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `cNameNeu`    VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              PRIMARY KEY (`kTagMapping`),
              KEY `cName` (`kSprache`,`cName`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }
}
