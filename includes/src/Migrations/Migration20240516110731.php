<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240516110731
 */
class Migration20240516110731 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create indizes for log tables';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        if ($this->db->getSingleObject('SHOW INDEX FROM `tjtllog` WHERE Key_name = \'idx_dErstellt\'') === null) {
            $this->execute('TRUNCATE TABLE `tjtllog`');
            $this->execute('ALTER TABLE `tjtllog` ADD INDEX `idx_dErstellt` (`dErstellt`)');
        }
        if ($this->db->getSingleObject('SHOW INDEX FROM `tzahlungslog` WHERE Key_name = \'idx_dDatum\'') === null) {
            $this->execute('TRUNCATE TABLE `tzahlungslog`');
            $this->execute('ALTER TABLE `tzahlungslog` ADD INDEX `idx_dDatum` (`dDatum`)');
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        if ($this->db->getSingleObject('SHOW INDEX FROM `tjtllog` WHERE Key_name = \'idx_dErstellt\'') !== null) {
            $this->execute('ALTER TABLE `tjtllog` DROP INDEX `idx_dErstellt`');
        }
        if ($this->db->getSingleObject('SHOW INDEX FROM `tzahlungslog` WHERE Key_name = \'idx_dDatum\'') !== null) {
            $this->execute('ALTER TABLE `tzahlungslog` DROP INDEX `idx_dDatum`');
        }
    }
}
