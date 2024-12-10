<?php

/**
 * add lang key redeemed coupons
 *
 * @author msc
 * @created Wed, 01 Feb 2017 15:36:18 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170201153618
 */
class Migration20170201153618 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    public function getDescription(): string
    {
        return 'Add lang key redeemed coupons';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'checkout', 'currentCoupon', 'Bereits eingelöster Kupon: ');
        $this->setLocalization('eng', 'checkout', 'currentCoupon', 'Redeemed coupon: ');
        $this->setLocalization('ger', 'checkout', 'discountForArticle', 'gültig für: ');
        $this->setLocalization('eng', 'checkout', 'discountForArticle', 'applied to: ');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('currentCoupon');
        $this->removeLocalization('discountForArticle');
    }
}
