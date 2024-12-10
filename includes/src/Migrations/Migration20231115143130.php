<?php

/**
 * Alter tpreis.kpreis to BIGINT
 *
 * @author fp
 * @created Wed, 15 Nov 2023 14:31:30 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20231115143130
 */
class Migration20231115143130 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Alter tpreis.kpreis to BIGINT';
    }

    /**
     * @param string $type
     * @return \stdClass[]
     */
    private function getPriceTablesByType(string $type): array
    {
        return $this->db->getObjects(
            "SELECT TABLE_NAME, COLUMN_NAME, EXTRA
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = :schemaName
                    AND TABLE_NAME IN ('tpreis', 'tpreisdetail')
                    AND COLUMN_NAME IN ('kPreis', 'kPreisDetail')
                    AND DATA_TYPE = :dataType",
            [
                'schemaName' => \DB_NAME,
                'dataType'   => \strtolower($type),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $tables = [];
        foreach ($this->getPriceTablesByType('int') as $tableDef) {
            $tables[] = $tableDef->TABLE_NAME;
            $this->execute(
                'ALTER TABLE `' . $tableDef->TABLE_NAME . '`
                    CHANGE COLUMN `' . $tableDef->COLUMN_NAME . '` `' . $tableDef->COLUMN_NAME . '`
                    BIGINT ' . $tableDef->EXTRA
            );
        }
        foreach (\array_unique($tables) as $tableName) {
            $this->execute('ANALYZE TABLE `' . $tableName . '`');
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $tables = [];
        foreach ($this->getPriceTablesByType('bigint') as $tableDef) {
            $tables[] = $tableDef->TABLE_NAME;
            $this->execute(
                'ALTER TABLE `' . $tableDef->TABLE_NAME . '`
                    CHANGE COLUMN `' . $tableDef->COLUMN_NAME . '` `' . $tableDef->COLUMN_NAME . '`
                    INT ' . $tableDef->EXTRA
            );
        }
        foreach (\array_unique($tables) as $tableName) {
            $this->execute('ANALYZE TABLE `' . $tableName . '`');
        }
    }
}
