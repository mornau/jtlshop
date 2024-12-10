<?php

/**
 * Create index for tzahlungslog
 *
 * @author fp
 * @created Tue, 05 Mar 2019 09:51:16 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190305095116
 */
class Migration20190305095116 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    /** @lang text */
    public function getDescription(): string
    {
        return 'Create index for tzahlungslog';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE tzahlungslog ADD INDEX idx_tzahlungslog_module (cModulId, nLevel)'
        );
    }


    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE tzahlungslog DROP INDEX idx_tzahlungslog_module'
        );
    }
}
