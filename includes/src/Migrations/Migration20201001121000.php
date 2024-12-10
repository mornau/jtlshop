<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201001121000
 */
class Migration20201001121000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove nTransparenzfarbe from tbrandingeinstellung';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tbrandingeinstellung` DROP COLUMN `nTransparenzfarbe`');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tbrandingeinstellung` ADD COLUMN `nTransparenzfarbe` TINYINT UNSIGNED NOT NULL');
    }
}
