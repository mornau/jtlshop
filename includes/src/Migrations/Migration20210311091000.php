<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210311091000
 */
class Migration20210311091000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add target column to tlink';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $columns = $this->getDB()->getSingleObject("SHOW COLUMNS FROM `tlink` LIKE 'target'");
        if ($columns === null) {
            $this->execute("ALTER TABLE tlink ADD COLUMN target VARCHAR(20) NOT NULL DEFAULT '_self'");
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tlink DROP COLUMN target');
    }
}
