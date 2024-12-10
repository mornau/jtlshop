<?php

/**
 * Create status table for or-filtered attributes
 *
 * @author fp
 * @created Wed, 19 Sep 2018 13:05:19 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180919130519
 */
class Migration20180919130519 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create indices for or-filtered attributes';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $duplicates = $this->getDB()->getObjects(
            'SELECT kMerkmal, kMerkmalWert, kArtikel, COUNT(*) cntData
                FROM tartikelmerkmal
                GROUP BY kMerkmal, kMerkmalWert, kArtikel
                HAVING COUNT(*) > 1'
        );
        foreach ($duplicates as $duplicate) {
            $this->getDB()->queryPrepared(
                'DELETE FROM tartikelmerkmal
                    WHERE kMerkmal = :attribID AND kMerkmalWert = :valueID AND kArtikel = :ProductID
                    LIMIT :delCount',
                [
                    'attribID' => $duplicate->kMerkmal,
                    'valueID' => $duplicate->kMerkmalWert,
                    'ProductID' => $duplicate->kArtikel,
                    'delCount' => $duplicate->cntData - 1,
                ]
            );
        }
        $this->execute(
            'ALTER TABLE tartikelmerkmal ADD UNIQUE KEY kArtikelMerkmalWert_UQ (kArtikel, kMerkmalWert, kMerkmal)'
        );
        $this->execute(
            'ALTER TABLE tartikel ADD UNIQUE KEY kVaterArtikel_UQ (kArtikel, nIstVater, kVaterArtikel)'
        );
        $this->execute(
            'ALTER TABLE tkategorieartikel ADD UNIQUE KEY kKategorieArtikel_UQ (kArtikel, kKategorie)'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tartikelmerkmal DROP INDEX kArtikelMerkmalWert_UQ');
        $this->execute('ALTER TABLE tartikel DROP INDEX kVaterArtikel_UQ');
        $this->execute('ALTER TABLE tkategorieartikel DROP INDEX kKategorieArtikel_UQ');
    }
}
