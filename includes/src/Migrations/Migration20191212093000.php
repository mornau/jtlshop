<?php

/**
 * Wizard setup data
 *
 * @author mh
 * @created Thu, 12 Dec 2019 09:30:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Shopsetting;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191212093000
 */
class Migration20191212093000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Wizard setup data';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        if (Shopsetting::getInstance()->getValue(\CONF_GLOBAL, 'global_wizard_done') === null) {
            $this->setConfig(
                'global_wizard_done',
                'Y',
                \CONF_GLOBAL,
                'Einrichtungsassistent durchlaufen',
                'selectbox',
                1,
                (object)[
                    'cBeschreibung'     => 'Einrichtungsassistent durchlaufen',
                    'inputOptions'      => [
                        'Y' => 'Ja',
                        'N' => 'Nein',
                    ],
                    'nStandardAnzeigen' => 0
                ]
            );
        }
        $this->execute("INSERT INTO `tadminrecht` (`cRecht`, `cBeschreibung`) VALUES ('WIZARD_VIEW', 'Set up wizard')");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('global_wizard_done');
        $this->execute("DELETE FROM `tadminrecht` WHERE `cRecht` = 'WIZARD_VIEW'");
    }
}
