<?php

/**
 * Extend column cName in teinheit
 *
 * @author sl
 * @created Thu, 14 Dec 2023 10:24:15 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20231214102415
 */
class Migration20231214102415 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sl';
    }

    public function getDescription(): string
    {
        return 'Extend column cName in teinheit';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `teinheit` CHANGE COLUMN `cName` `cName` VARCHAR(255)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `teinheit` CHANGE COLUMN `cName` `cName` VARCHAR(20)');
    }
}
