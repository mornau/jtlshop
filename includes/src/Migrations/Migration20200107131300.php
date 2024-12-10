<?php

/**
 * adds lang var to wishlist section
 *
 * @author ms
 * @created Mon, 07 Jan 2020 13:13:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200107131300
 */
class Migration20200107131300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add lang var to wishlist section';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'wishlist', 'addCurrentProductsToCart', 'aktuelle Artikel in den Warenkorb');
        $this->setLocalization('eng', 'wishlist', 'addCurrentProductsToCart', 'add current products to cart');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('addCurrentProductsToCart');
    }
}
