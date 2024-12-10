<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240213160200
 */
class Migration20240213160200 extends Migration implements IMigration
{
    /**
     * @inheritdoc
     */
    public function getAuthor(): string
    {
        return 'fm';
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return 'Add column "type" to tredirect table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->db->executeQuery(
            'ALTER TABLE `tredirect` 
                ADD COLUMN `type` INT(10) NOT NULL DEFAULT 0;'
        );
        $this->db->executeQuery(
            'ALTER TABLE `tredirect` 
                ADD COLUMN `dateCreated` DATETIME NULL DEFAULT NOW();'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->db->executeQuery('ALTER TABLE `tredirect` DROP COLUMN `type`');
        $this->db->executeQuery('ALTER TABLE `tredirect` DROP COLUMN `dateCreated`');
    }
}
