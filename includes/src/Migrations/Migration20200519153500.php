<?php

/**
 * @author mh
 * @created Tue, 19 May 2020 15:35:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200519153500
 */
class Migration20200519153500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Misc frontend lang fixes';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'basket',
            'noShippingCostsReached',
            'Ihre Bestellung ist mit %s versandkostenfrei nach %s lieferbar.'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'noShippingCostsReached',
            'Your order can be shipped for free with %s to %s.'
        );
        $this->setLocalization(
            'ger',
            'basket',
            'noShippingCostsAt',
            'Noch %s und wir versenden kostenfrei mit %s nach %s.'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'noShippingCostsAt',
            'Another %s and your order will be eligible for free shipping with %s to %s.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization(
            'ger',
            'basket',
            'noShippingCostsReached',
            'Ihre Bestellung ist mit %s versandkostenfrei %s lieferbar.'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'noShippingCostsReached',
            'Your order can be shipped for free with %s %s.'
        );
        $this->setLocalization(
            'ger',
            'basket',
            'noShippingCostsAt',
            'Noch %s und wir versenden kostenfrei mit %s %s'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'noShippingCostsAt',
            'Another %s and your order will be eligible for free shipping with %s %s'
        );
    }
}
