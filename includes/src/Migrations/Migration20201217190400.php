<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;
use stdClass;

/**
 * Class Migration20201217190400
 */
class Migration20201217190400 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add samesite cookie option None';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $sectionID                = $this->getDB()->getSingleInt(
            'SELECT kEinstellungenConf
                FROM teinstellungenconf
                    WHERE cWertName = \'global_cookie_samesite\'',
            'kEinstellungenConf'
        );
        $conf                     = new stdClass();
        $conf->kEinstellungenConf = $sectionID;
        $conf->cName              = 'None';
        $conf->cWert              = 'None';
        $conf->nSort              = 5;
        $this->getDB()->insert('teinstellungenconfwerte', $conf);
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'DELETE FROM teinstellungenconfwerte
                WHERE cName = \'None\' 
                AND cWert = \'None\'
                AND nSort = 5'
        );
    }
}
