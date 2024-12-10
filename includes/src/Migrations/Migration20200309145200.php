<?php

/**
 * Add lang var coupon success
 *
 * @author mh
 * @created Mon, 09 Mar 2020 14:52:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200309145200
 */
class Migration20200309145200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add lang var coupon success';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'couponSuccess', 'Der Coupon wurde freigeschaltet.');
        $this->setLocalization('eng', 'global', 'couponSuccess', 'Your coupon has been activated.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('couponSuccess');
    }
}
