<?php

/**
 * add_indices_at_cCode_from_tkupon
 *
 * @author msc
 * @created Fri, 15 Jul 2016 11:32:29 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160715113229
 */
class Migration20160715113229 extends Migration implements IMigration
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
        $this->execute('ALTER TABLE `tkupon` ADD INDEX(`cCode`)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tkupon DROP INDEX cCode');
    }
}
