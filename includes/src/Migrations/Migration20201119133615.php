<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201119133615
 */
class Migration20201119133615 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Recreate missing autoincrement attributes';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        foreach (
            [
                'tkontaktbetreff' => [
                    'column' => 'kKontaktBetreff',
                    'refs'   => [
                        'tkontaktbetreffsprache',
                        'tkontakthistory'
                    ]
                ],
                'tsprachiso'      => [
                    'column' => 'kSprachISO',
                    'refs'   => [
                        'tsprachlog',
                        'tsprachwerte'
                    ]
                ],
                'ttext'           => [
                    'column' => 'kText'
                ],
            ] as $table => $keyDef
        ) {
            $keyColumn = $keyDef['column'];
            $lastValue = $this->db->getSingleObject(
                'SELECT COALESCE(MAX(' . $keyColumn . '), 0) + 1 AS value FROM ' . $table
            );
            $zeroKey   = $this->db->getSingleObject(
                'SELECT ' . $keyColumn . ' AS value FROM ' . $table . ' WHERE ' . $keyColumn . ' = 0',
            );
            if ($lastValue === null) {
                continue;
            }
            if ($zeroKey !== null) {
                $zeroKey->value   = (int)$lastValue->value;
                $lastValue->value = (int)$lastValue->value + 1;
                $this->db->update($table, [$keyColumn], [0], (object)[$keyColumn => $zeroKey->value]);
                if (isset($keyDef['refs'])) {
                    foreach ($keyDef['refs'] as $ref) {
                        $this->db->update($ref, [$keyColumn], [0], (object)[$keyColumn => $zeroKey->value]);
                    }
                }
            }
            $this->execute(
                'ALTER TABLE ' . $table
                . ' CHANGE COLUMN ' . $keyColumn . ' ' . $keyColumn
                . ' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT'
            );
            $this->execute(
                'ALTER TABLE ' . $table . ' AUTO_INCREMENT ' . $lastValue->value
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
