<?php

/**
 * Refactor data types
 * @author  fp
 * @created Mon, 20 Aug 2018 11:26:05 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180820112605
 */
class Migration20180820112605 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Refactor data types for kKundengruppe';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $columns = $this->fetchAll(
            "SELECT TABLE_NAME, COLUMN_TYPE, COLUMN_DEFAULT, IS_NULLABLE
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = '" . DB_NAME . "'
                    AND COLUMN_NAME = 'kKundengruppe'
                    AND DATA_TYPE = 'tinyint'
                    AND TABLE_NAME NOT LIKE 'xplugin_%'"
        );
        foreach ($columns as $column) {
            $sql = /** @lang text */
                'ALTER TABLE `' . DB_NAME . '`.`' . $column->TABLE_NAME . '` CHANGE `kKundengruppe` `kKundengruppe` INT'
                . (str_contains($column->COLUMN_TYPE, 'unsigned') ? ' UNSIGNED' : '')
                . ($column->IS_NULLABLE === 'YES' ? ' NULL' : ' NOT NULL')
                . ($column->COLUMN_DEFAULT === null || $column->COLUMN_DEFAULT === 'NULL'
                    ? ($column->IS_NULLABLE === 'YES' ? ' DEFAULT NULL' : '')
                    : ' DEFAULT \'' . $column->COLUMN_DEFAULT . '\'');

            $this->execute($sql);
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        // can not be undone...
    }
}
