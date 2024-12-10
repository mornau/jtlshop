<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200407143100
 */
class Migration20200407143100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add extension store id to plugins';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tplugin` ADD COLUMN `exsID` VARCHAR(255) NULL DEFAULT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tplugin` DROP COLUMN `exsID`');
    }
}
