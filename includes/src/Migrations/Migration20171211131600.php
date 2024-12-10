<?php

/** missing migration for manufacturer filter. sets coupon manufacturer filter if empty*/

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20171211131600
 */
class Migration20171211131600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE tkupon SET cHersteller = '-1' WHERE cHersteller = '';");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
