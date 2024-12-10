<?php

/**
 * Update lang var productInflowing
 *
 * @author mh
 * @created Fr, 12 Jun 2020 15:00:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200612150000
 */
class Migration20200612150000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Update lang var productInflowing';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'productDetails',
            'productInflowing',
            'Ware bestellt. %s %s voraussichtlich ab dem %s verfügbar.'
        );
        $this->setLocalization('eng', 'productDetails', 'productInflowing', 'Goods ordered. %s %s expected on %s.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'productDetails', 'productInflowing', '%s bestellt, am %s erwartet');
        $this->setLocalization('eng', 'productDetails', 'productInflowing', '%s ordered, expected on %s');
    }
}
