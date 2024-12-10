<?php

/**
 * Create column nMehrfachauswahl for tmerkmal
 *
 * @author fm
 * @created Thu, 11 Mai 2017 15:34:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170511153400
 */
class Migration20170511153400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    /** @lang text */
    public function getDescription(): string
    {
        return 'Create column nMehrfachauswahl in tmerkmal';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE tmerkmal ADD COLUMN nMehrfachauswahl TINYINT NOT NULL DEFAULT 0'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE tmerkmal DROP COLUMN nMehrfachauswahl'
        );
    }
}
