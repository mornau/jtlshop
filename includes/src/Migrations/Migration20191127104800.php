<?php

/**
 * Add lang shipping info
 *
 * @author mh
 * @created Wed, 27 Nov 2019 10:48:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191127104800
 */
class Migration20191127104800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add lang shipping info';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'productDetails',
            'shippingInformation',
            'Die angegebenen Lieferzeiten gelten für den Versand innerhalb von %s. Die Lieferzeiten für den ' .
            "Versand ins Ausland finden Sie in unseren <a href=\'%s\'>Versandinformationen</a>."
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'shippingInformation',
            'The indicated delivery times refer to shipments within %s. For information on the delivery times ' .
            "for shipments to other countries, please see the  <a href=\'%s\'>Shipping information</a>."
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('shippingInformation');
    }
}
