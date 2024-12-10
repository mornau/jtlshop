<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210212114600
 */
class Migration20210212114600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add system page flag';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tlink` ADD COLUMN `bIsSystem` TINYINT(1) NOT NULL DEFAULT 0');
        $this->execute('ALTER TABLE `tlinkgruppe` ADD COLUMN `bIsSystem` TINYINT(1) NOT NULL DEFAULT 0');
        $this->execute('UPDATE `tlink` SET bIsSystem = 1 WHERE nLinkart IN (SELECT nLinkart FROM tspezialseite)');
        $this->execute(
            'UPDATE `tlinkgruppe` SET bIsSystem = 1 
            WHERE cTemplatename IN (\'Kopf\', \'hidden\', \'Fuss\', \'megamenu\')'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tlink` DROP COLUMN `bIsSystem`');
    }
}
