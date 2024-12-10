<?php

/**
 * Rebuild ttrennzeichen and add unique index
 *
 * @author fm
 * @created Wed, 18 Jan 2018 16:20:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Catalog\Separator;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180124162000
 */
class Migration20180124162000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Rebuild ttrennzeichen and add unique index';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        Separator::migrateUpdate();
        $this->execute('ALTER TABLE `ttrennzeichen` ADD UNIQUE INDEX `unique_lang_unit` (`kSprache`, `nEinheit`)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `ttrennzeichen` DROP INDEX `unique_lang_unit`');
    }
}
