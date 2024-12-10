<?php

/**
 * adds lang var for price flow title
 *
 * @author ms
 * @created Fri, 06 Dec 2019 14:33:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191206130000
 */
class Migration20191206130000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add lang var for price flow title';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'productDetails', 'PriceFlowTitle', 'Preisverlauf der letzten %s Monate');
        $this->setLocalization('eng', 'productDetails', 'PriceFlowTitle', 'price flow of the last %s months');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('PriceFlowTitle');
    }
}
