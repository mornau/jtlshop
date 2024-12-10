<?php

/**
 * adds paymentNotNecessary language variable
 *
 * @author ms
 * @created Wed, 10 May 2017 14:53:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170510145300
 */
class Migration20170510145300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add paymentNotNecessary language variable';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'checkout', 'paymentNotNecessary', 'Keine Zahlung notwendig');
        $this->setLocalization('eng', 'checkout', 'paymentNotNecessary', 'Payment not necessary');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('paymentNotNecessary');
    }
}
