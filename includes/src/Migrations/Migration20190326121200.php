<?php

/**
 * @author fm
 * @created Tue, 26 Mar 2019 12:12:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190326121200
 */
class Migration20190326121200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Change nVersion type';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tpluginmigration` CHANGE COLUMN `nVersion` `nVersion` VARCHAR(255) NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tpluginmigration` CHANGE COLUMN `nVersion` `nVersion` int(3) NOT NULL');
    }
}
