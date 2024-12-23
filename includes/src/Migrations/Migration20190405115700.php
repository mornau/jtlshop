<?php

/**
 * @author fm
 * @created Fri, 05 Apr 2019 11:57:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190405115700
 */
class Migration20190405115700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove trusted shops';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $boxIDs = $this->getDB()->getObjects('SELECT kBox FROM tboxen WHERE kBoxvorlage IN (17, 18)');
        foreach ($boxIDs as $boxID) {
            $this->getDB()->delete('tboxensichtbar', 'kBox', $boxID->kBox);
            $this->getDB()->delete('tboxsprache', 'kBox', $boxID->kBox);
        }
        $this->execute('DELETE FROM tboxvorlage WHERE kBoxvorlage IN (17, 18)');
        $this->execute('DELETE FROM tboxen WHERE kBoxvorlage IN (17, 18)');
        $configs = $this->getDB()->getObjects(
            'SELECT kEinstellungenConf AS id FROM teinstellungenconf WHERE kEinstellungenSektion = 117'
        );
        foreach ($configs as $config) {
            $this->getDB()->delete('teinstellungenconfwerte', 'kEinstellungenConf', $config->id);
        }
        $this->execute('DELETE FROM teinstellungen WHERE kEinstellungenSektion = 117');
        $this->execute('DELETE FROM teinstellungenconf WHERE kEinstellungenSektion = 117');
        $this->execute("DELETE FROM tadminrecht WHERE cRecht = 'ORDER_TRUSTEDSHOPS_VIEW'");
        $this->execute("DELETE FROM tadminrechtegruppe WHERE cRecht = 'ORDER_TRUSTEDSHOPS_VIEW'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
