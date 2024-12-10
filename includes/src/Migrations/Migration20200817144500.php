<?php

/**
 * Add favourable shipping lang
 *
 * @author mh
 * @created Mon, 27 July 2020 15:01:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200817144500
 */
class Migration20200817144500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add favourable shipping lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'basket',
            'shippingInformationSpecificSingle',
            'Zzgl. <a href="%1$s" class="shipment popup">Versandkosten</a>: %2$s bei Lieferung nach %3$s'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'shippingInformationSpecificSingle',
            'Plus <a href="%1$s" class="shipment popup">shipping costs</a>: %2$s for delivery to %3$s'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('shippingInformationSpecificSingle', 'basket');
    }
}
