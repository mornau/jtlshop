<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Shop;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200903121000
 */
class Migration20200903121000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add country manager';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "INSERT INTO `tadminrecht` (`cRecht`, `cBeschreibung`)
                VALUES ('COUNTRY_VIEW', 'Country manager')"
        );
        $this->execute(
            "ALTER TABLE `tland`
                ADD COLUMN `bPermitRegistration` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'"
        );
        $this->execute(
            "ALTER TABLE `tland`
                ADD COLUMN `bRequireStateDefinition` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'"
        );

        if ($this->getDB()->getSingleInt('SELECT COUNT(*) AS cnt FROM tversandart', 'cnt') === 0) {
            // if no shipping methods are set (most likely for new installation),
            // permit all countries in European Economic Area
            $this->execute(
                "UPDATE `tland`
                    SET `bPermitRegistration` = 1
                    WHERE cISO IN ('BE', 'BG', 'DN', 'DE', 'EE', 'FI', "
                . "'FR', 'GR', 'IE', 'IT', 'HR', 'LV', 'LT', 'LU', "
                . "'MT', 'NL', 'AT', 'PL', 'PT', 'RO', 'SE', 'SK', 'SI', 'ES', "
                . "'CZ', 'HU', 'CY', 'IS', 'LI', 'NO')"
            );
        } elseif (Shop::getSettingValue(\CONF_KUNDEN, 'kundenregistrierung_nur_lieferlaender') === 'Y') {
            $this->execute(
                "UPDATE tland
                    INNER JOIN tversandart
                      ON tversandart.cLaender RLIKE CONCAT(tland.cISO, ' ')
                    SET tland.bPermitRegistration = 1
                    WHERE tland.bPermitRegistration = 0"
            );
        } else {
            $this->execute('UPDATE `tland` SET `bPermitRegistration` = 1');
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tadminrecht` WHERE `cRecht` = 'COUNTRY_VIEW'");
        $this->execute('ALTER TABLE `tland` DROP COLUMN `bPermitRegistration`');
        $this->execute('ALTER TABLE `tland` DROP COLUMN `bRequireStateDefinition`');
    }
}
