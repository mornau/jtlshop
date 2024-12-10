<?php

/**
 * Update admin bootstrap template in database
 *
 * @author msc
 * @created Mon, 27 Aug 2018 09:11:16 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180827091116
 */
class Migration20180827091116 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    public function getDescription(): string
    {
        return 'Update admin bootstrap template in database';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        // Moved to Migration20180801124135
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
