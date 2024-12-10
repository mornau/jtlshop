<?php

/**
 * Change text to mediumtext for tnewsletter
 *
 * @author fp
 * @created Thu, 09 Mar 2017 15:12:22 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170309151222
 */
class Migration20170309151222 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Change text to mediumtext for tnewsletter';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE tnewsletter
                CHANGE COLUMN cInhaltHTML cInhaltHTML MEDIUMTEXT NOT NULL,
                CHANGE COLUMN cInhaltText cInhaltText MEDIUMTEXT NOT NULL'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE tnewsletter
                CHANGE COLUMN cInhaltHTML cInhaltHTML TEXT NOT NULL,
                CHANGE COLUMN cInhaltText cInhaltText TEXT NOT NULL'
        );
    }
}
