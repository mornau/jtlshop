<?php

/**
 * @author fm
 * @created Thu, 17 Oct 2019 11:08:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191017110800
 */
class Migration20191017110800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add safe mode language vars';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'global',
            'safeModeActive',
            'Abgesicherter Modus aktiv. Gewisse Funktionalitäten stehen nicht zur Verfügung.'
        );
        $this->setLocalization(
            'eng',
            'global',
            'safeModeActive',
            'Safe mode enabled. Certain functionality will not be available.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('safeModeActive');
    }
}
