<?php

/**
 * Change "Amazon Payments" to "Amazon Pay"
 *
 * @author dr
 * @created Tue, 14 Mar 2017 11:01:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170314110100
 */
class Migration20170314110100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Change "Amazon Payments" to "Amazon Pay"';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE tadminmenu SET cLinkname = 'Amazon Pay' WHERE cLinkname = 'Amazon Payments'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE tadminmenu SET cLinkname = 'Amazon Payments' WHERE cLinkname = 'Amazon Pay'");
    }
}
