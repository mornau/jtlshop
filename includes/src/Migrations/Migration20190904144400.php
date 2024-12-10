<?php

/**
 * @author fm
 * @created Wed, 04 Sep 2019 14:44:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190904144400
 */
class Migration20190904144400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add input type for plugin language variables';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("ALTER TABLE `tpluginsprachvariable` ADD COLUMN `type` VARCHAR(255) NOT NULL DEFAULT 'text';");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tpluginsprachvariable` DROP COLUMN `type`;');
    }
}
