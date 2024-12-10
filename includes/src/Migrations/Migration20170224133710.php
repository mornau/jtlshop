<?php

/**
 * Upgrade sessiondata to MEDIUMTEXT
 *
 * @author fp
 * @created Fri, 24 Feb 2017 13:37:10 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170224133710
 */
class Migration20170224133710 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Upgrade sessiondata to MEDIUMTEXT';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE tsession
                CHANGE COLUMN cSessionData cSessionData MEDIUMTEXT NULL DEFAULT NULL'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        // In case of downgrade all sessions will be deleted to prevent invalid session data by truncating.
        $this->execute(
            'DELETE FROM tsession'
        );
        $this->execute(
            'ALTER TABLE tsession
                CHANGE COLUMN cSessionData cSessionData TEXT NULL DEFAULT NULL'
        );
    }
}
