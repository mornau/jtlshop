<?php

/**
 * Add answer column to tbewertung
 *
 * @author dr
 * @created Tue, 07 Mar 2017 17:00:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170307170000
 */
class Migration20170307170000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add answer column to tbewertung';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tbewertung ADD COLUMN cAntwort TEXT AFTER dDatum');
        $this->execute('ALTER TABLE tbewertung ADD COLUMN dAntwortDatum DATE AFTER cAntwort');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropColumn('tbewertung', 'dAntwortDatum');
        $this->dropColumn('tbewertung', 'cAntwort');
    }
}
