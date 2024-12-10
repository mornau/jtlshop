<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20231020133000
 */
class Migration20231020133000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove cType from teigenschaftwertpict/tkategoriepict';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `teigenschaftwertpict` DROP COLUMN `cType`');
        $this->execute('ALTER TABLE `tkategoriepict` DROP COLUMN `cType`');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `teigenschaftwertpict` ADD COLUMN `cType` CHAR(1) DEFAULT NULL');
        $this->execute('ALTER TABLE `tkategoriepict` ADD COLUMN `cType` CHAR(1) DEFAULT NULL');
    }
}
