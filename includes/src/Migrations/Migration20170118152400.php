<?php

/**
 * Adds account language variables
 *
 * @author ms
 * @created Wed, 18 Jan 2017 15:24:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170118152400
 */
class Migration20170118152400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Adds language variables account section';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'account data', 'accountOverview', 'Übersicht');
        $this->setLocalization('eng', 'account data', 'accountOverview', 'Overview');

        $this->setLocalization('ger', 'account data', 'orders', 'Bestellungen');
        $this->setLocalization('eng', 'account data', 'orders', 'Orders');

        $this->setLocalization('ger', 'account data', 'addresses', 'Adressen');
        $this->setLocalization('eng', 'account data', 'addresses', 'Addresses');

        $this->setLocalization('ger', 'account data', 'wishlists', 'Wunschlisten');
        $this->setLocalization('eng', 'account data', 'wishlists', 'Wishlists');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('accountOverview');
        $this->removeLocalization('orders');
        $this->removeLocalization('addresses');
        $this->removeLocalization('wishlists');
    }
}
