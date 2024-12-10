<?php

/**
 * @author ms
 * @created Thu, 14 May 2020 14:05:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200514140500
 */
class Migration20200514140500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add lang var for finance costs';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'order', 'financeCosts', 'zzgl. Finanzierungskosten');
        $this->setLocalization('eng', 'order', 'financeCosts', 'plus finance costs');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('financeCosts', 'order');
    }
}
