<?php

/**
 * new category structure
 *
 * @author fm
 * @created Mo, 11 Apr 2016 17:16:10 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160411171610
 */
class Migration20160411171610 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE `tartikelabnahme`
                CHANGE COLUMN `fMindestabnahme` `fMindestabnahme` DOUBLE NULL DEFAULT '0';
            UPDATE `tartikelabnahme`
                SET `fMindestabnahme` = CAST(fMindestabnahme AS DECIMAL(10,4))
                WHERE kArtikel > 0 AND fMindestabnahme > 0;"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "ALTER TABLE `tartikelabnahme`
                CHANGE COLUMN `fMindestabnahme` `fMindestabnahme` FLOAT NULL DEFAULT '0'"
        );
    }
}
