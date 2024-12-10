<?php

/**
 * change the column type of tkupon.cArtikel to MEDIUMTEXT to store more product numbers than just about 5000
 *
 * @author dr
 * @created Mon, 01 Nov 2016 08:26:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161101082600
 */
class Migration20161101082600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tkupon MODIFY cArtikel MEDIUMTEXT NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tkupon MODIFY cArtikel TEXT NOT NULL');
    }
}
