<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220223145100
 */
class Migration20220223145100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Template preview';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE `ttemplate` 
                CHANGE COLUMN `eTyp` `eTyp` ENUM('standard', 'mobil', 'admin', 'test') NOT NULL"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "ALTER TABLE `ttemplate` 
                CHANGE COLUMN `eTyp` `eTyp` ENUM('standard', 'mobil', 'admin') NOT NULL"
        );
    }
}
