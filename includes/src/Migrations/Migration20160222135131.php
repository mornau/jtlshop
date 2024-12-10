<?php

/**
 * add column cMail to tkuponkunde
 *
 * @author sh
 * @created Mon, 22 Feb 2016 13:51:31 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160222135131
 */
class Migration20160222135131 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'sh';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tkuponkunde ADD `cMail` VARCHAR(255) AFTER `kKunde`');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropColumn('tkuponkunde', 'cMail');
    }
}
