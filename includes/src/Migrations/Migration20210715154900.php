<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210715154900
 */
class Migration20210715154900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Better coupon description lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'checkout', 'useCoupon', 'Aktionscoupon einlösen');
        $this->setLocalization('eng', 'checkout', 'useCoupon', 'Redeem promotional coupon');
        $this->setLocalization(
            'ger',
            'checkout',
            'couponFormDesc',
            'Kostenfrei ausgestellten Aktionscoupon/Werbecoupon einlösen.'
        );
        $this->setLocalization('eng', 'checkout', 'couponFormDesc', 'Redeem a free promotional coupon.');
        $this->setLocalization('ger', 'checkout', 'couponCodePlaceholder', 'Coupon-Code eingeben');
        $this->setLocalization('eng', 'checkout', 'couponCodePlaceholder', 'Enter coupon code');
        $this->setLocalization('ger', 'checkout', 'couponSubmit', 'Coupon einlösen');
        $this->setLocalization('eng', 'checkout', 'couponSubmit', 'Redeem coupon');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'checkout', 'useCoupon', 'Coupon einlösen');
        $this->setLocalization('eng', 'checkout', 'useCoupon', 'Redeem coupon');

        $this->removeLocalization('couponFormDesc', 'checkout');
        $this->removeLocalization('couponCodePlaceholder', 'checkout');
        $this->removeLocalization('couponSubmit', 'checkout');
    }
}
