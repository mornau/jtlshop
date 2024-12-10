<?php

/**
 * Remove review orphans and recalculate review values
 *
 * @author sl
 * @created Fri, 14 Oct 2022 11:17:16 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20221014111716
 */
class Migration20221014111716 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sl';
    }

    public function getDescription(): string
    {
        return 'Remove review orphans and recalculate review values';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'DELETE tartikelext
                FROM tartikelext
                    LEFT JOIN  tbewertung ON tartikelext.kArtikel = tbewertung.kArtikel
                                            AND tbewertung.nAktiv = 1
                WHERE tbewertung.kArtikel IS NULL'
        );

        $this->execute(
            'INSERT INTO tartikelext (kArtikel, fDurchschnittsBewertung)
                SELECT * FROM (
                    SELECT kArtikel, ROUND(SUM(nSterne) / COUNT(kBewertung), 1) AS avgStars
                    FROM tbewertung WHERE nAktiv = 1
                    GROUP BY kArtikel
                ) AS new
                ON DUPLICATE KEY UPDATE fDurchschnittsBewertung = new.avgStars'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        //not necessary
    }
}
