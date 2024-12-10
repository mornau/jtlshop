<?php

/**
 * Rename tbesucher.cSessId to tbesucher.cSessID
 *
 * @author dr
 * @created Mon, 20 Jun 2016 13:41:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160620134100
 */
class Migration20160620134100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tbesucher` CHANGE COLUMN `cSessId` `cSessID` VARCHAR(128)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tbesucher` CHANGE COLUMN `cSessID` `cSessId` VARCHAR(128)');
    }
}
