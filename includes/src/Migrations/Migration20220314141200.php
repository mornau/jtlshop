<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220314141200
 */
class Migration20220314141200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add key teinstellungen default';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->getDB()->queryPrepared(
            "DELETE FROM `teinstellungen_default`
                WHERE `cName` = 'vergleichsliste_anzeigen'
                  AND kEinstellungenSektion = :section",
            ['section' => \CONF_VERGLEICHSLISTE]
        );
        $this->execute('CREATE UNIQUE INDEX sectionName ON teinstellungen_default(kEinstellungenSektion, cName);');
        $this->getDB()->queryPrepared(
            "INSERT IGNORE INTO `teinstellungen_default` VALUES (:section, 'vergleichsliste_anzeigen', 'Y', NULL)",
            ['section' => \CONF_VERGLEICHSLISTE]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP INDEX sectionName ON teinstellungen_default');
    }
}
