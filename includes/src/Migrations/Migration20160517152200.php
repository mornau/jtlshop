<?php

/**
 * update language vars for coupons in polls
 *
 * @author ms
 * @created Tue, 17 May 2016 15:22:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160517152200
 */
class Migration20160517152200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'messages',
            'pollCoupon',
            'Vielen Dank für die Teilnahme an unserer Umfrage. '
            . 'Für Ihre nächste Bestellung steht Ihnen der folgende Kuponcode zur Verfügung: %s.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'pollCoupon',
            'Thank you for taking part in our poll. '
            . 'For your next order, feel free to use the following coupon code: %s.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization(
            'ger',
            'messages',
            'pollCoupon',
            'Vielen Dank für die Teilnahme an unserer Umfrage. Ihnen wurde der Kupon %s gutgeschrieben.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'pollCoupon',
            'Your poll was successfully saved and the coupon %s was credited to you, thank you.'
        );
    }
}
