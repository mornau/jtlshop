<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220201113000
 */
class Migration20220201113000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Create sqlite3 caching option';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "INSERT INTO teinstellungenconfwerte (kEinstellungenConf, cName, cWert, nSort)
            VALUES ('1551','SQLite','sqlite','10')"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM teinstellungenconfwerte WHERE cName = 'SQLite' AND kEinstellungenConf = 1551");
    }
}
