<?php

/**
 * changes language variables noShippingCosts
 *
 * @author ms
 * @created Tue, 11 Oct 2016 16:11:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161011161100
 */
class Migration20161011161100 extends Migration implements IMigration
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
        $this->setLocalization('ger', 'basket', 'noShippingCostsAt', 'Noch %s und wir versenden kostenfrei mit %s %s');
        $this->setLocalization(
            'eng',
            'basket',
            'noShippingCostsAt',
            'Buy for another %s and get no shipping costs with %s %s'
        );

        $this->setLocalization('ger', 'basket', 'noShippingCostsAtExtended', 'innerhalb von %s');
        $this->setLocalization('eng', 'basket', 'noShippingCostsAtExtended', 'to: %s');

        $this->setLocalization(
            'ger',
            'basket',
            'noShippingCostsReached',
            'Ihre Bestellung ist ohne Versandkosten mit %s %s'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'noShippingCostsReached',
            'Your order has no shipping costs with %s %s'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'basket', 'noShippingCostsAt', 'Noch %s und wir versenden kostenfrei');
        $this->setLocalization('eng', 'basket', 'noShippingCostsAt', 'Buy for another %s and get no shipping costs');

        $this->setLocalization('ger', 'basket', 'noShippingCostsAtExtended', 'Innerhalb von %s');
        $this->setLocalization('eng', 'basket', 'noShippingCostsAtExtended', 'To: %s');

        $this->setLocalization('ger', 'basket', 'noShippingCostsReached', 'Ihre Bestellung ist ohne Versandkosten');
        $this->setLocalization('eng', 'basket', 'noShippingCostsReached', 'Your order has no shipping costs');
    }
}
