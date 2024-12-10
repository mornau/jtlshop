<?php

/**
 * renaming_sortPriceAsc_and_sortPriceDesc
 *
 * @author msc
 * @created Fri, 08 Jul 2016 14:14:28 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160708141428
 */
class Migration20160708141428 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'sortPriceAsc', 'Preis aufsteigend');
        $this->setLocalization('eng', 'global', 'sortPriceAsc', 'Price ascending');
        $this->setLocalization('ger', 'global', 'sortPriceDesc', 'Preis absteigend');
        $this->setLocalization('eng', 'global', 'sortPriceDesc', 'Price descending');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'global', 'sortPriceAsc', 'Preis 1..9');
        $this->setLocalization('eng', 'global', 'sortPriceAsc', 'Price 1..9');
        $this->setLocalization('ger', 'global', 'sortPriceDesc', 'Preis 9..1');
        $this->setLocalization('eng', 'global', 'sortPriceDesc', 'Price 9..1');
    }
}
