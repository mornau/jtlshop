<?php

/**
 * add_delivery_status_lang
 *
 * @author mh
 * @created Fri, 03 Aug 2018 12:52:35 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180803125235
 */
class Migration20180803125235 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add delivery status lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'productDetails',
            'productUnsaleable',
            'Dieser Artikel ist derzeit nicht verfügbar. '
            . 'Ob und wann dieser Artikel wieder erhältlich ist, steht nicht fest.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'productUnsaleable',
            'This product is currently unavailable. '
            . 'It is uncertain whether or when the product will be available again.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('productUnsaleable');
    }
}
