<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210526141500
 */
class Migration20210526141500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add no coupon lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'checkout',
            'couponUnavailable',
            'Für den aktuellen Inhalt des Warenkorbs ' .
            'existiert kein verfügbarer Coupon.'
        );
        $this->setLocalization('eng', 'checkout', 'couponUnavailable', 'No coupon available for current basket.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('couponUnavailable', 'checkout');
    }
}
