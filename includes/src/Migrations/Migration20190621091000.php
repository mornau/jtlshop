<?php

/**
 * Add lang nova checkout
 *
 * @author mh
 * @created Fri, 21 June 2019 09:10:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190621091000
 */
class Migration20190621091000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add lang nova checkout';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'change', 'Ändern');
        $this->setLocalization('eng', 'global', 'change', 'Change');
        $this->setLocalization('ger', 'checkout', 'shippingTo', 'Versand nach');
        $this->setLocalization('eng', 'checkout', 'shippingTo', 'Shipping to');
        $this->setLocalization('ger', 'checkout', 'secureCheckout', 'Secure Checkout');
        $this->setLocalization('eng', 'checkout', 'secureCheckout', 'Secure Checkout');
        $this->setLocalization(
            'ger',
            'checkout',
            'guestOrRegistered',
            'Sie können als Gast bestellen oder einen neuen Account erstellen.'
        );
        $this->setLocalization('eng', 'checkout', 'guestOrRegistered', 'Proceed as guest or create a new account.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('change');
        $this->removeLocalization('shippingTo');
        $this->removeLocalization('secureCheckout');
        $this->removeLocalization('guestOrRegistered');
    }
}
