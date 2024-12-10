<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210713150000
 */
class Migration20210713150000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add delete filter lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'deleteFilter', 'Diesen Filter entfernen');
        $this->setLocalization('eng', 'global', 'deleteFilter', 'Remove this filter');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('deleteFilter', 'global');
    }
}
