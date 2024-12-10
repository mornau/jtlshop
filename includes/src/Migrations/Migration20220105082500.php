<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220105082500
 */
class Migration20220105082500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Better shipping country cost note';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'productDetails', 'shippingInfoIcon', '(%s - Ausland abweichend)');
        $this->setLocalization('eng', 'productDetails', 'shippingInfoIcon', '(%s - int. shipments may differ)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'productDetails', 'shippingInfoIcon', 'Ausland');
        $this->setLocalization('eng', 'productDetails', 'shippingInfoIcon', 'Other countries');
    }
}
