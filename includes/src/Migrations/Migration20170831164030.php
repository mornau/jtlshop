<?php

/**
 * create_order_info_about_downloadeable_products
 *
 * @author msc
 * @created Thu, 31 Aug 2017 16:40:30 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170831164030
 */
class Migration20170831164030 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    public function getDescription(): string
    {
        return 'Create order info about downloadeable products';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'checkout',
            'digitalProductsRegisterInfo',
            'Nur angemeldete Kunden können Download-Artikel bestellen. '
            . 'Bitte erstellen Sie ein Kundenkonto oder melden Sie sich mit Ihren Zugangsdaten an, '
            . 'um mit dem Kauf fortzufahren.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'digitalProductsRegisterInfo',
            'Only registered customers can order downloadable products. '
            . 'Please register or log in to your account in order to continue your purchase.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('digitalProductsRegisterInfo');
    }
}
