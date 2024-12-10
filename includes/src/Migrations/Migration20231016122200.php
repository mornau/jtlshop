<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20231016122200
 */
class Migration20231016122200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove APC caching method - reverted 2024-01-08';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
