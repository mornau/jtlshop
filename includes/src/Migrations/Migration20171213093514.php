<?php

/**
 * Delete unused fulltext keys
 *
 * @author fp
 * @created Wed, 13 Dec 2017 09:35:14 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20171213093514
 */
class Migration20171213093514 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fpr';
    }

    public function getDescription(): string
    {
        return 'Delete unused fulltext keys';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        foreach (['tartikel', 'tartikelsprache'] as $table) {
            $keys = $this->fetchAll(
                \sprintf(
                    "SHOW INDEX FROM `%s`
                    WHERE Index_type = 'FULLTEXT'
                    AND Column_name IN ('cBeschreibung', 'cKurzBeschreibung')
                    AND Key_name != 'idx_%s_fulltext'",
                    $table,
                    $table
                )
            );
            foreach ($keys as $key) {
                $this->execute(
                    \sprintf(
                        'ALTER TABLE %s DROP KEY %s',
                        $table,
                        $key->Key_name
                    )
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        foreach (['tartikel', 'tartikelsprache'] as $table) {
            foreach (['cBeschreibung', 'cKurzBeschreibung'] as $fieldName) {
                $this->execute(
                    \sprintf(
                        'ALTER TABLE `%s`
                        ADD FULLTEXT KEY `%s` (`%s`)',
                        $table,
                        $fieldName,
                        $fieldName
                    )
                );
            }
        }
    }
}
