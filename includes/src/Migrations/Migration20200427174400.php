<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200427174400
 */
class Migration20200427174400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add template table rows';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `ttemplate` 
                ADD COLUMN `exsID` VARCHAR(255) NULL DEFAULT NULL AFTER `preview`,
                ADD COLUMN `bootstrap` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `exsID`,
                ADD COLUMN `framework` VARCHAR(255) NULL DEFAULT NULL AFTER `bootstrap`'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `ttemplate` DROP COLUMN `exsID`, DROP COLUMN `bootstrap`, DROP COLUMN `framework`');
    }
}
