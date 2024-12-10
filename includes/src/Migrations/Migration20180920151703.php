<?php

/**
 * remove_price_radar
 *
 * @author mh
 * @created Thu, 20 Sep 2018 15:17:03 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180920151703
 */
class Migration20180920151703 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove Priceradar';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "DELETE tboxvorlage, tboxen, tboxensichtbar, tboxsprache
                FROM tboxvorlage
                LEFT JOIN tboxen 
                    ON tboxen.kBoxvorlage = tboxvorlage.kBoxvorlage
                LEFT JOIN tboxensichtbar
                    ON tboxensichtbar.kBox = tboxen.kBox
                LEFT JOIN tboxsprache
                    ON tboxsprache.kBox = tboxen.kBox
                WHERE tboxvorlage.cTemplate = 'box_priceradar.tpl'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO tboxvorlage 
                  (kBoxvorlage, kCustomID, eTyp, cName, cVerfuegbar, cTemplate) 
                VALUES (100, 0, 'tpl', 'Preisradar', '0', 'box_priceradar.tpl')"
        );
    }
}
