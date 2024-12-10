<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220308152700
 */
class Migration20220509083700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add Back-to-list language variable';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'global',
            'goBackToList',
            'Zurück zur Liste'
        );
        $this->setLocalization(
            'eng',
            'global',
            'goBackToList',
            'Back to list'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('goBackToList', 'global');
    }
}
