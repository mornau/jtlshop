<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230831134600
 */
class Migration20230831134600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add API Keys permission';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "INSERT INTO `tadminrecht` (`cRecht`, `cBeschreibung`)
            VALUES ('API_KEYS_VIEW', 'API Keys')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tadminrecht` WHERE `cRecht` = 'API_KEYS_VIEW'");
    }
}
