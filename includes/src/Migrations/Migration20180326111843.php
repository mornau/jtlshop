<?php

/**
 * Add new field for responsibilty of cart position.
 *
 * @author fp
 * @created Mon, 26 Mar 2018 11:18:43 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180326111843
 */
class Migration20180326111843 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Add new field for responsibility of cart position.';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE twarenkorbpos
                ADD COLUMN cResponsibility VARCHAR(255) NOT NULL DEFAULT 'core' AFTER cUnique"
        );
        $this->execute(
            "ALTER TABLE twarenkorbperspos
                ADD COLUMN cResponsibility VARCHAR(255) NOT NULL DEFAULT 'core' AFTER cUnique"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE twarenkorbpos DROP COLUMN cResponsibility'
        );
        $this->execute(
            'ALTER TABLE twarenkorbperspos DROP COLUMN cResponsibility'
        );
    }
}
