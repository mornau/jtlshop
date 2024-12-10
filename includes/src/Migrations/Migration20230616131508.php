<?php

/**
 * Changes fAnzahl in tlieferscheinpos from INT to DOUBLE
 *
 * @author Tim Niko Tegtmeyer
 * @created Fri, 16 Jun 2023 13:15:08 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230616131508
 */
class Migration20230616131508 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'Tim Niko Tegtmeyer';
    }

    public function getDescription(): string
    {
        return 'Changes fAnzahl in tlieferscheinpos from INT to DOUBLE';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tlieferscheinpos` CHANGE COLUMN `fAnzahl` `fAnzahl` DOUBLE UNSIGNED NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tlieferscheinpos` CHANGE COLUMN `fAnzahl` `fAnzahl` INT UNSIGNED NOT NULL');
    }
}
