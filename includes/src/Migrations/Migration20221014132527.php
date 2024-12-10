<?php

/**
 * Create index for newsletter
 *
 * @author fp
 * @created Fri, 14 Oct 2022 13:25:27 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20221014132527
 */
class Migration20221014132527 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create index for newsletter';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        if ($this->db->getSingleObject("SHOW INDEX FROM toptin WHERE KEY_NAME = 'idx_cMail'") === null) {
            $this->db->executeQuery('ALTER TABLE toptin ADD INDEX idx_cMail (cMail)');
        }
        if (
            $this->db->getSingleObject(
                "SHOW INDEX FROM tnewsletterempfaengerhistory WHERE KEY_NAME = 'idx_cEmail_cAktion'"
            ) === null
        ) {
            $this->db->executeQuery(
                'ALTER TABLE tnewsletterempfaengerhistory
                    ADD INDEX idx_cEmail_cAktion (cEmail, cAktion)'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        if ($this->db->getSingleObject("SHOW INDEX FROM toptin WHERE KEY_NAME = 'idx_cMail'") !== null) {
            $this->db->executeQuery('ALTER TABLE toptin DROP INDEX idx_cMail');
        }
        if (
            $this->db->getSingleObject(
                "SHOW INDEX FROM tnewsletterempfaengerhistory WHERE KEY_NAME = 'idx_cEmail_cAktion'"
            ) !== null
        ) {
            $this->db->executeQuery('ALTER TABLE tnewsletterempfaengerhistory DROP INDEX idx_cEmail_cAktion');
        }
    }
}
