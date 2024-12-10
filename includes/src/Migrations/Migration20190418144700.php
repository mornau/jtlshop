<?php

/**
 * syntax checks
 *
 * @author fm
 * @created Thu, 18 Apr 2019 14:47:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190418144700
 */
class Migration20190418144700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Syntax checks';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        // moved to Migration_20190901000000 for sequence reasons
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
