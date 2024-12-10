<?php

/** add a manufacturer column to tkupon to enable manufacturer specific coupons*/

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161108112500
 */
class Migration20161108112500 extends Migration implements IMigration
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
        $this->execute('ALTER TABLE tkupon ADD COLUMN cHersteller TEXT NOT NULL AFTER cArtikel;');

        $this->setLocalization(
            'ger',
            'global',
            'couponErr12',
            'Der Kupon ist für den aktuellen Warenkorb ungültig (gilt nur für bestimmte Hersteller).'
        );
        $this->setLocalization(
            'eng',
            'global',
            'couponErr12',
            'This coupon is invalid for your cart (valid only for specific manufacturers).'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropColumn('tkupon', 'cHersteller');
        $this->removeLocalization('couponErr12');
    }
}
