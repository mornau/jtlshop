<?php

/**
 * Add sending progress col to tnewsletter
 *
 * @author cr
 * @created Wed, 10 Jul 2019 09:05:52 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * class Migration20190710090552
 */
class Migration20190710090552 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Add sending progress col to tnewsletter';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE `tnewsletter`
                ADD COLUMN `dLastSendings`
                    DATETIME
                    DEFAULT NULL
                    COMMENT 'finish time of last sending of this NL'
                    AFTER `dStartZeit`"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tnewsletter` DROP COLUMN `dLastSendings`');
    }
}
