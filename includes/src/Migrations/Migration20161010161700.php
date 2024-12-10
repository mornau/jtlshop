<?php

/**
 * add shipping language variable
 *
 * @author msc
 * @created Thu, 10 Oct 2016 16:17:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161010161700
 */
class Migration20161010161700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'checkout', 'productShippingDesc', 'Gesonderte Versandkosten');
        $this->setLocalization('eng', 'checkout', 'productShippingDesc', 'Separate shipping costs');
        $this->setLocalization('ger', 'global', 'shippingMethods', 'Versandarten');
        $this->setLocalization('eng', 'global', 'shippingMethods', 'Shipping methods');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization(
            'ger',
            'checkout',
            'productShippingDesc',
            'Für folgende Artikel gelten folgende Versandkosten'
        );
        $this->setLocalization('eng', 'checkout', 'productShippingDesc', 'Shipping costs for the following products');
        $this->removeLocalization('shippingMethods');
    }
}
