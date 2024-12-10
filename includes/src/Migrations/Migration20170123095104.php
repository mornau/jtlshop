<?php

/**
 * delete giropay in tzahlungsartsprache
 *
 * @author msc
 * @created Mon, 23 Jan 2017 09:51:04 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170123095104
 */
class Migration20170123095104 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('DELETE FROM `tzahlungsartsprache` WHERE `kZahlungsart` = 0');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        // Not necessary
    }
}
