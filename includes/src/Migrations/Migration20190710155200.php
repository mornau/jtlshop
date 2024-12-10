<?php

/**
 * Add aria labels
 *
 * @author ms
 * @created Wed, 10 July 2019 15:52:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190710155200
 */
class Migration20190710155200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add aria labels';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'aria', 'scrollMenuRight', 'nach rechts scrollen');
        $this->setLocalization('eng', 'aria', 'scrollMenuRight', 'scroll to the right');

        $this->setLocalization('ger', 'aria', 'scrollMenuLeft', 'nach links scrollen');
        $this->setLocalization('eng', 'aria', 'scrollMenuLeft', 'scroll to the left');

        $this->setLocalization('ger', 'aria', 'wishlistOptions', 'Wunschzettelmenü');
        $this->setLocalization('eng', 'aria', 'wishlistOptions', 'wishlist options');

        $this->setLocalization('ger', 'productOverview', 'differentialPriceFrom', 'Preis ab');
        $this->setLocalization('eng', 'productOverview', 'differentialPriceFrom', 'price starts at');

        $this->setLocalization('ger', 'productOverview', 'differentialPriceTo', 'Preis bis');
        $this->setLocalization('eng', 'productOverview', 'differentialPriceTo', 'price ends at');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('scrollMenuRight');
        $this->removeLocalization('scrollMenuLeft');
        $this->removeLocalization('wishlistOptions');
        $this->removeLocalization('differentialPriceFrom');
        $this->removeLocalization('differentialPriceTo');
    }
}
