<?php

/**
 * change the column type of tlinkgruppensprache.kLinkgruppe to INT
 *
 * @author ms
 * @created Tue, 09 Nov 2016 11:18:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161109111800
 */
class Migration20161109111800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE tlinkgruppesprache
                CHANGE COLUMN kLinkgruppe kLinkgruppe INT UNSIGNED NOT NULL DEFAULT '0';"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "ALTER TABLE tlinkgruppesprache
                CHANGE COLUMN kLinkgruppe kLinkgruppe TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';"
        );
    }
}
