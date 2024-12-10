<?php

/**
 * Remove tkategorieartikelgesamt
 *
 * @author fp
 * @created Tue, 20 Jun 2017 10:35:19 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170620103519
 */
class Migration20170620103519 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Remove tkategorieartikelgesamt';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('DROP TABLE tkategorieartikelgesamt');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'CREATE TABLE tkategorieartikelgesamt (
                kArtikel       int(10) unsigned NOT NULL,
                kOberKategorie int(10) unsigned NOT NULL,
                kKategorie     int(10) unsigned NOT NULL,
                nLevel         int(10) unsigned NOT NULL,
                KEY kArtikel (kArtikel,kOberKategorie)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
        $this->execute(
            'INSERT INTO tkategorieartikelgesamt (kArtikel, kOberKategorie, kKategorie, nLevel) (
            SELECT DISTINCT tkategorieartikel.kArtikel, oberkategorie.kOberKategorie, 
                            oberkategorie.kKategorie, oberkategorie.nLevel - 1
                FROM tkategorieartikel
                INNER JOIN tkategorie ON tkategorie.kKategorie = tkategorieartikel.kKategorie
                INNER JOIN tkategorie oberkategorie ON tkategorie.lft BETWEEN oberkategorie.lft AND oberkategorie.rght
            )'
        );
    }
}
