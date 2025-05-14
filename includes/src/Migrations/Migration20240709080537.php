<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240709080537
 */
class Migration20240709080537 extends Migration implements IMigration
{
    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return 'dr';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'add customer groups column to opcpage';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("ALTER TABLE topcpage ADD COLUMN customerGroups MEDIUMTEXT DEFAULT NULL");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("ALTER TABLE topcpage DROP COLUMN customerGroups");
    }
}
