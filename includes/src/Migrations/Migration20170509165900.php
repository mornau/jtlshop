<?php

/**
 * Add available column to redirect table
 *
 * @author dr
 * @created Tue, 09 May 2017 17:00:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170509165900
 */
class Migration20170509165900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add available column to redirect table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE tredirect
                ADD COLUMN cAvailable CHAR(1) DEFAULT 'u'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE tredirect
                DROP COLUMN bAvailable'
        );
    }
}
