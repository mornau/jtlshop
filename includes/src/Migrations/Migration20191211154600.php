<?php

/**
 * Correct shipping estimate lang
 *
 * @author mh
 * @created Wed, 9 Oct 2019 11:21:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191211154600
 */
class Migration20191211154600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Correct shipping estimate lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'checkout',
            'estimateShippingCostsTo',
            'Versandkostenermittlung für aktuellen Warenkorbinhalt nach'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'estimateShippingCostsTo',
            'Determine shipping costs for current basket to'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'checkout', 'estimateShippingCostsTo', 'Versandkostenermittlung nach');
        $this->setLocalization('eng', 'checkout', 'estimateShippingCostsTo', 'Determine shipping costs according to');
    }
}
