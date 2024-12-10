<?php

/**
 * Change default value of setting "bewertungserinnerung_nutzen" to "B"
 *
 * @author sl
 * @created Mon, 17 Oct 2022 14:46:35 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20221017144635
 */
class Migration20221017144635 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sl';
    }

    public function getDescription(): string
    {
        return 'Change default value of setting "bewertungserinnerung_nutzen" to "B"';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE teinstellungen_default SET cWERT = 'B' WHERE cName = 'bewertungserinnerung_nutzen'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE teinstellungen_default SET cWERT = 'Y' WHERE cName = 'bewertungserinnerung_nutzen'");
    }
}
