<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191029125000
 */
class Migration20191029125000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Remove tkuponneukunde_backup';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $tables = $this->fetchAll("SHOW TABLES LIKE 'tkuponkunde_backup'");

        if (\count($tables) > 0) {
            /** @var \stdClass $backupData */
            $backupData = $this->fetchOne(
                'SELECT COUNT(*) cntBack
                    FROM (SELECT tkuponkunde_backup.kKupon,
                            SHA2(LOWER(tkuponkunde_backup.cMail), 256) AS cMail
                            FROM tkuponkunde_backup
                            INNER JOIN tkupon
                                    ON tkupon.kKupon = tkuponkunde_backup.kKupon
                            WHERE tkuponkunde_backup.cMail != \'\'
                            GROUP BY tkuponkunde_backup.cMail, tkuponkunde_backup.kKupon) back
                    LEFT JOIN tkuponkunde ON tkuponkunde.kKupon = back.kKupon
                             AND CONVERT(tkuponkunde.cMail USING utf8) = CONVERT(back.cMail USING utf8)'
            );

            if ((int)$backupData->cntBack === 0) {
                $this->execute('DROP TABLE tkuponkunde_backup');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
