<?php

/**
 * Lang var for price change during checkout.
 *
 * @author fp
 * @created Fri, 24 May 2019 12:23:22 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Migration
 */
class Migration20190524122322 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create lang var for price change during checkout.';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'checkout',
            'priceHasChanged',
            'Der Preis für den Artikel "%s" in Ihrem Warenkorb '
            . 'hat sich zwischenzeitlich geändert.  Bitte prüfen Sie die Warenkorbpositionen.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'priceHasChanged',
            'The price for the article "%s" in your basket has '
            . 'changed in the meantime. Please check your order items.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('priceHasChanged');
    }
}
