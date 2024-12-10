<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210412123100
 */
class Migration20210412123100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add lang comparelist delete all';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'comparelist', 'comparelistDeleteAll', 'Alle Artikel löschen');
        $this->setLocalization('eng', 'comparelist', 'comparelistDeleteAll', 'Remove all items');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('comparelistDeleteAll', 'comparelist');
    }
}
