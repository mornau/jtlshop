<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220726133200
 */
class Migration20220726133200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Removed table tbesuchersuchausdruecke';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('DROP TABLE IF EXISTS `tbesuchersuchausdruecke`');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'CREATE TABLE IF NOT EXISTS `tbesuchersuchausdruecke` (
              `kBesucher` int(10) unsigned NOT NULL,
              `cSuchanfrage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `cRohdaten` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              KEY `cSuchanfrage` (`cSuchanfrage`),
              KEY `kBesucher` (`kBesucher`)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }
}
