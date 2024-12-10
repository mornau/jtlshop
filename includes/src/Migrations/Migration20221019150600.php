<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

class Migration20221019150600 extends Migration implements IMigration
{
    public function up(): void
    {
        $this->setLocalization('ger', 'wishlist', 'infoItemsFound', '%s Artikel wurden zu Ihrer Suche gefunden.');
        $this->setLocalization('eng', 'wishlist', 'infoItemsFound', '%s products found.');
    }

    public function down(): void
    {
        $this->removeLocalization('infoItemsFound', 'wishlist');
    }
}
