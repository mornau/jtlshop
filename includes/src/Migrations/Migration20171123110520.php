<?php

/**
 * add_lang_key_shipping_information
 *
 * @author msc
 * @created Thu, 23 Nov 2017 11:05:20 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20171123110520
 */
class Migration20171123110520 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    public function getDescription(): string
    {
        return 'Add lang key shipping information';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'basket',
            'shippingInformationSpecific',
            'Zzgl. <a href="%1$s" class="shipment popup">Versandkosten</a> ab %2$s bei Lieferung nach %3$s'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'shippingInformationSpecific',
            'Plus <a href="%1$s" class="shipment popup">shipping costs</a> starting from %2$s for delivery to %3$s'
        );
        $this->setLocalization(
            'ger',
            'basket',
            'shippingInformation',
            'Zzgl. <a href="%1$s" class="shipment popup">Versandkosten</a>'
        );
        $this->setLocalization(
            'eng',
            'basket',
            'shippingInformation',
            'Plus <a href="%1$s" class="shipment popup">shipping costs</a>'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('shippingInformationSpecific');
        $this->removeLocalization('shippingInformation');
    }
}
