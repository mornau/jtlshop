<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220222092000
 */
class Migration20220222092000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Show cron option';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE teinstellungenconf SET nStandardAnzeigen = 1 WHERE cWertName = 'cron_freq'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE teinstellungenconf SET nStandardAnzeigen = 0 WHERE cWertName = 'cron_freq'");
    }
}
