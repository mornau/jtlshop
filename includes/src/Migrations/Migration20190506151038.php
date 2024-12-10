<?php

/**
 * Create unique index for tseo
 *
 * @author fp
 * @created Mon, 06 May 2019 15:10:38 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190506151038
 */
class Migration20190506151038 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create unique index for tseo';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'DELETE FROM tseo
                 WHERE cSeo IN (SELECT * FROM (
                    SELECT DISTINCT tseo1.cSeo
                    FROM tseo tseo1
                    LEFT JOIN tseo tseo2 ON tseo2.cKey = tseo1.cKey
                        AND tseo2.kKey = tseo1.kKey
                        AND tseo2.kSprache = tseo1.kSprache
                        AND tseo2.cSeo < tseo1.cSeo
                    WHERE tseo2.cSeo IS NOT NULL) AS i)'
        );
        if ($this->fetchOne("SHOW INDEX FROM tseo WHERE KEY_NAME = 'cKey'")) {
            $this->execute('DROP INDEX cKey ON tseo');
        }
        $this->execute('CREATE UNIQUE INDEX cKey ON tseo(cKey, kKey, kSprache)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        if ($this->fetchOne("SHOW INDEX FROM tseo WHERE KEY_NAME = 'cKey'")) {
            $this->execute('DROP INDEX cKey ON tseo');
        }
        $this->execute('CREATE INDEX cKey ON tseo(cKey, kKey, kSprache)');
    }
}
