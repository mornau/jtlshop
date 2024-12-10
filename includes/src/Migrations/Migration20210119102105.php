<?php

/**
 * remove UK from EU in tland
 *
 * @author cr
 * @created Tue, 19 Jan 2021 10:21:05 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210119102105
 */
class Migration20210119102105 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'remove UK from EU in tland';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE tland SET nEU=0 WHERE cISO='GB'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE tland SET nEU=1 WHERE cISO='GB'");
    }
}
