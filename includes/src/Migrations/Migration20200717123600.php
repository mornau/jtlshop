<?php

/**
 * Increase versandklasse varchar size
 *
 * @author mh
 * @created Fr, 17 July 2020 12:36:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200717123600
 */
class Migration20200717123600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Increase versandklasse varchar size';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tversandart` DROP INDEX cVersandklassen');
        $this->execute('ALTER TABLE `tversandart` DROP INDEX cKundengruppen');
        $this->execute('ALTER TABLE `tversandart` MODIFY COLUMN `cVersandklassen` VARCHAR (8192)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tversandart` MODIFY COLUMN `cVersandklassen` VARCHAR (255)');
        if (!$this->getDB()->getSingleObject("SHOW INDEX FROM tversandart WHERE KEY_NAME = 'cVersandklassen'")) {
            $this->execute('ALTER TABLE `tversandart` ADD INDEX cVersandklassen (cVersandklassen)');
        }
        if (!$this->getDB()->getSingleObject("SHOW INDEX FROM tversandart WHERE KEY_NAME = 'cKundengruppen'")) {
            $this->execute('ALTER TABLE `tversandart` ADD INDEX cKundengruppen (cKundengruppen)');
        }
    }
}
