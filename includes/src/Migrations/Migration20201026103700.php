<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201026103700
 */
class Migration20201026103700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Plugin migrations unique key';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `tpluginmigration` 
                ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT,
                DROP PRIMARY KEY,
                ADD PRIMARY KEY (`id`),
                ADD UNIQUE INDEX `plgn_migid` (`kMigration` ASC, `pluginID` ASC)'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE `tpluginmigration` 
                DROP COLUMN `id`,
                DROP PRIMARY KEY,
                ADD PRIMARY KEY (`kMigration`),
                DROP INDEX `plgn_migid`'
        );
    }
}
