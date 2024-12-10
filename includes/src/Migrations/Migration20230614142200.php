<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230614142200
 */
class Migration20230614142200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'add customer group id to tverfuegbarkeitsbenachrichtigung';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `tverfuegbarkeitsbenachrichtigung` 
                ADD COLUMN `customerGroupID` INT NOT NULL DEFAULT 0'
        );
        $this->execute(
            'UPDATE `tverfuegbarkeitsbenachrichtigung`
                SET `customerGroupID` = (SELECT kKundengruppe FROM tkundengruppe WHERE cStandard = \'Y\')'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE `tverfuegbarkeitsbenachrichtigung`
                DROP COLUMN `customerGroupID`'
        );
    }
}
