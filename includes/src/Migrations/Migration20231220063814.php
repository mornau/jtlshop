<?php

/**
 * Save Consent Statistics in database
 *
 * @author tnt
 * @created Wed, 20 Dec 2023 06:38:14 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240320124941
 */
class Migration20231220063814 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'tnt';
    }

    public function getDescription(): string
    {
        return 'Save Consent Statistics in database';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE `consent_statistics` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `visitorID` INT UNSIGNED NOT NULL,
                `eventDate` DATE NOT NULL,
                `eventName` VARCHAR(24) NOT NULL,
                `eventValue` TINYINT NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `event_visitor_UNIQUE` (`visitorID`, `eventName`),
                INDEX `idx_eventDate_eventName` (`eventDate`, `eventName`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
        );

        $this->execute(
            'INSERT IGNORE INTO `tadminrecht` (`cRecht`, `cBeschreibung`)
                VALUES (\'STATS_CONSENT_VIEW\', \'Consent-Manager\');'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS `consent_statistics`;');
        $this->execute(
            'DELETE `tadminrecht`, `tadminrechtegruppe`
                FROM `tadminrecht`
                LEFT JOIN `tadminrechtegruppe` ON `tadminrechtegruppe`.`cRecht` = `tadminrecht`.`cRecht`
                WHERE `tadminrecht`.`cRecht` = \'STATS_CONSENT_VIEW\''
        );
    }
}
