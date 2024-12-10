<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * add_lang_var_sort
 *
 * @author ms
 * @created Tue, 12 Mar 2019 15:51:00 +0100
 */

/**
 * Class Migration20190312155100
 */
class Migration20190312155100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add lang var for sort';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'filterAndSort', 'Filter & Sortierung');
        $this->setLocalization('eng', 'global', 'filterAndSort', 'filters & sorting');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('filterAndSort');
    }
}
