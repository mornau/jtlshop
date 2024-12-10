<?php

/**
 * syntax checks
 *
 * @author fm
 * @created Mon, 16 May 2019 12:23:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190506122300
 */
class Migration20190506122300 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Link references';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tlink` ADD COLUMN `reference` INT(10) UNSIGNED NOT NULL DEFAULT 0');
        $references = $this->getDB()->getObjects(
            "SELECT kLink, cName
                FROM tlink
                WHERE cName RLIKE 'Referenz [0-9]+'
                  AND nLinkart = :linkartReference",
            ['linkartReference' => LINKTYP_REFERENZ]
        );
        foreach ($references as $reference) {
            if (\preg_match('/Referenz ([\d]+)/', $reference->cName, $hits)) {
                $this->getDB()->update('tlink', 'kLink', $reference->kLink, (object)['reference' => (int)$hits[1]]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tlink` DROP COLUMN `reference`');
    }
}
