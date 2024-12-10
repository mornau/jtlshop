<?php

/**
 * Correct shipping to lang var
 *
 * @author mh
 * @created Wed, 11 Dec 2019 11:15:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191211111500
 */
class Migration20191211111500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Correct shipping to lang var';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'checkout', 'shippingTo', 'Versand nach');
        $this->setLocalization('eng', 'checkout', 'shippingTo', 'Destination');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
