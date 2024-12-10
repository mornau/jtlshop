<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210615052500
 */
class Migration20210615052500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add voucher lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'productDetails', 'voucherFlexPlaceholder', 'Gutscheinwert in %s');
        $this->setLocalization('eng', 'productDetails', 'voucherFlexPlaceholder', 'Voucher value in %s');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('voucherFlexPlaceholder', 'productDetails');
    }
}
