<?php

/**
 * Change nSort to INT instead of TINYINT.
 *
 * @author fp
 * @created Wed, 23 Sep 2020 09:06:29 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200923090629
 */
class Migration20200923090629 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Change nSort to INT instead of TINYINT.';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tsuchcachetreffer MODIFY nSort int signed DEFAULT 0 NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('TRUNCATE TABLE tsuchcachetreffer');
        $this->execute('TRUNCATE TABLE tsuchcache');
        $this->execute('ALTER TABLE tsuchcachetreffer MODIFY nSort tinyint unsigned DEFAULT 0 NOT NULL');
    }
}
