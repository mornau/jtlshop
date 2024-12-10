<?php

/**
 * add lang vars for increase decrease buttons
 *
 * @author ms
 * @created Thu, 14 Nov 2019 14:33:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191114143300
 */
class Migration20191114143300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add lang vars for increase decrease buttons';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'aria', 'increaseQuantity', 'Menge erhöhen');
        $this->setLocalization('eng', 'aria', 'increaseQuantity', 'increase quantity');

        $this->setLocalization('ger', 'aria', 'decreaseQuantity', 'Menge verringern');
        $this->setLocalization('eng', 'aria', 'decreaseQuantity', 'decrease quantity');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('increaseQuantity');
        $this->removeLocalization('decreaseQuantity');
    }
}
