<?php

/**
 * changed some language-values for assets
 *
 * @author cr
 * @created Fri, 20 Apr 2018 12:35:20 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180420123520
 */
class Migration20180420123520 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Change language values for assets';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'account data', 'useCredit', 'Guthaben verrechnet');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'account data', 'useCredit', 'Guthaben verrechnen');
    }
}
