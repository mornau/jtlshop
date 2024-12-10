<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201005121800
 */
class Migration20201005121800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Disable old JTL Widgets plugin';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE tplugin SET nStatus = 1 WHERE cName = 'JTL Widgets' AND nVersion = 100 AND nStatus = 2");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
