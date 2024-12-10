<?php

/**
 * @author fm
 * @created Tue, 02 Apr 2019 11:19:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190402111900
 */
class Migration20190402111900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'kKampgne default value';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `timagemap` 
                CHANGE COLUMN `kKampagne` `kKampagne` INT(10) UNSIGNED NOT NULL DEFAULT 0'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `timagemap` CHANGE COLUMN `kKampagne` `kKampagne` INT(10) UNSIGNED NOT NULL');
    }
}
