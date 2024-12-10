<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220308152700
 */
class Migration20220308152700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add cart has parent items lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'checkout',
            'warningCartContainedParentItems',
            'Ihr Warenkorb enthielt Vaterartikel die nicht gekauft werden dürfen. '
            . 'Bitte prüfen sie die Warenkorbpositionen.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'warningCartContainedParentItems',
            'Your basket contained parent items which can not be purchased. Please check the line items in the basket.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('warningCartContainedParentItems', 'checkout');
    }
}
