<?php

/**
 * Add lang var for wishlist/comparelist buttons
 *
 * @author mh
 * @created Fri, 14 June 2019 09:49:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190614094900
 */
class Migration20190614094900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add lang var for wishlist/comparelist buttons';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'onWishlist', 'Auf Wunschzettel');
        $this->setLocalization('eng', 'global', 'onWishlist', 'On wishlist');
        $this->setLocalization('ger', 'global', 'notOnWishlist', 'Nicht auf Wunschzettel');
        $this->setLocalization('eng', 'global', 'notOnWishlist', 'Not on wishlist');
        $this->setLocalization('ger', 'global', 'onComparelist', 'Auf Vergleichsliste');
        $this->setLocalization('eng', 'global', 'onComparelist', 'On comparelist');
        $this->setLocalization('ger', 'global', 'notOnComparelist', 'Nicht auf Vergleichsliste');
        $this->setLocalization('eng', 'global', 'notOnComparelist', 'Not on comparelist');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('onWishlist');
        $this->removeLocalization('notOnWishlist');
        $this->removeLocalization('onComparelist');
        $this->removeLocalization('notOnComparelist');
    }
}
