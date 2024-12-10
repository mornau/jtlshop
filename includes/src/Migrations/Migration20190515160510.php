<?php

/**
 * Create unique index for tkundenattribut
 *
 * @author fp
 * @created Wed, 15 May 2019 16:05:10 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190515160510
 */
class Migration20190515160510 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create unique index for tkundenattribut';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'DELETE FROM tkundenattribut
                 WHERE kKundenAttribut IN (SELECT * FROM (
                    SELECT DISTINCT tkundenattribut1.kKundenAttribut
                    FROM tkundenattribut tkundenattribut1
                    LEFT JOIN tkundenattribut tkundenattribut2 ON tkundenattribut2.kKunde = tkundenattribut1.kKunde
                        AND tkundenattribut2.kKundenfeld = tkundenattribut1.kKundenfeld
                        AND tkundenattribut2.kKundenAttribut < tkundenattribut1.kKundenAttribut
                    WHERE tkundenattribut2.kKundenAttribut IS NOT NULL) AS i)'
        );
        if ($this->fetchOne("SHOW INDEX FROM tkundenattribut WHERE KEY_NAME = 'kKundenfeld'")) {
            $this->execute('DROP INDEX kKundenfeld ON tkundenattribut');
        }
        $this->execute('CREATE UNIQUE INDEX kKundenfeld ON tkundenattribut(kKunde, kKundenfeld)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        if ($this->fetchOne("SHOW INDEX FROM tkundenattribut WHERE KEY_NAME = 'kKundenfeld'")) {
            $this->execute('DROP INDEX kKundenfeld ON tkundenattribut');
        }
        $this->execute('CREATE INDEX kKundenfeld ON tkundenattribut(kKundenfeld)');
    }
}
