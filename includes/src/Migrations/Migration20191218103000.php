<?php

/**
 * Add lang var for filter
 *
 * @author mh
 * @created Wed, 18 Dec 2019 10:30:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191218103000
 */
class Migration20191218103000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add lang var for filter';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'filterCancel', 'Abbrechen');
        $this->setLocalization('eng', 'global', 'filterCancel', 'Cancel');
        $this->setLocalization('ger', 'global', 'filterShowItem', '%s Artikel ansehen');
        $this->setLocalization('eng', 'global', 'filterShowItem', 'Show %s items');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('filterCancel');
        $this->removeLocalization('filterShowItem');
    }
}
