<?php

/**
 * Alter tzahlungsinfo to represent sync status
 *
 * @author fp
 * @created Mon, 27 Feb 2017 10:04:40 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170227100440
 */
class Migration20170227100440 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Alter tzahlungsinfo to represent sync status';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE tzahlungsinfo
                ADD COLUMN cAbgeholt VARCHAR(1) NOT NULL DEFAULT 'N'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE tzahlungsinfo
                DROP COLUMN cAbgeholt'
        );
    }
}
