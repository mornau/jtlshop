<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201015144700
 */
class Migration20201015144700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add topbar lang var';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'topbarNote', '');
        $this->setLocalization('eng', 'global', 'topbarNote', '');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('topbarNote', 'global');
    }
}
