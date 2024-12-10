<?php

/**
 * add_boxen_bestseller_minanzahl_to_sektion_global
 *
 * @author msc
 * @created Tue, 23 Aug 2016 10:39:34 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160823103934
 */
class Migration20160823103934 extends Migration implements IMigration
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
        $this->execute(
            "UPDATE `teinstellungenconf`
                SET kEinstellungenSektion = 1,
                    cWertName = 'global_bestseller_minanzahl',
                    nSort = 285
                WHERE kEinstellungenConf = 1308"
        );
        $this->execute(
            "UPDATE `teinstellungen`
                SET kEinstellungenSektion = 1,
                    cName = 'global_bestseller_minanzahl'
                WHERE cName = 'boxen_bestseller_minanzahl'"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "UPDATE `teinstellungenconf`
                SET kEinstellungenSektion = 8,
                    cWertName = 'boxen_bestseller_minanzahl',
                    nSort = 140
                WHERE kEinstellungenConf = 1308"
        );
        $this->execute(
            "UPDATE `teinstellungen`
                SET kEinstellungenSektion = 8,
                    cName = 'boxen_bestseller_minanzahl'
                WHERE cName = 'global_bestseller_minanzahl'"
        );
    }
}
