<?php

/**
 * Change Voucher placeholder SHOP-5642
 *
 * @author sl
 * @created Tue, 06 Dec 2022 12:45:27 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20221206124527
 */
class Migration20221206124527 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sl';
    }

    public function getDescription(): string
    {
        return 'Change Voucher placeholder SHOP-5642';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'productDetails', 'voucherFlexPlaceholder', 'Gutscheinwert');
        $this->setLocalization('eng', 'productDetails', 'voucherFlexPlaceholder', 'Voucher value');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'productDetails', 'voucherFlexPlaceholder', 'Gutscheinwert in %s');
        $this->setLocalization('eng', 'productDetails', 'voucherFlexPlaceholder', 'Voucher value in %s');
    }
}
