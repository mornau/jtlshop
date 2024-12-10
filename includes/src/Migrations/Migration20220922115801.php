<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

class Migration20220922115801 extends Migration implements IMigration
{
    public function up(): void
    {
        $this->setLocalization('ger', 'productOverview', 'moreVariationsAvailable', 'Weitere Variationen erhältlich.');
        $this->setLocalization('eng', 'productOverview', 'moreVariationsAvailable', 'More variations available.');
    }

    public function down(): void
    {
        $this->removeLocalization('moreVariationsAvailable', 'productOverview');
    }
}
