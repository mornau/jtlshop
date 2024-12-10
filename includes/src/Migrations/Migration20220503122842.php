<?php

/**
 * Create setting for top selling
 *
 * @author fp
 * @created Tue, 03 May 2022 12:28:42 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use DateTime;
use JTL\Cron\Type;
use JTL\Update\IMigration;
use JTL\Update\Migration;
use stdClass;

/**
 * Class Migration20220503122842
 */
class Migration20220503122842 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create setting for top selling';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        if ($this->fetchOne("SHOW INDEX FROM tbestellung WHERE KEY_NAME = 'idx_dErstellt_WK'")) {
            $this->execute('DROP INDEX idx_dErstellt_WK ON tbestellung');
        }
        $this->execute('ALTER TABLE tbestellung ADD KEY idx_dErstellt_WK (dErstellt, cStatus, kWarenkorb)');
        $this->setConfig(
            'global_bestseller_tage',
            90,
            1,
            'Maximale Anzahl Tage für Bestseller',
            'number',
            286,
            (object)[
                'cBeschreibung' => 'Hier legen Sie fest, welcher zurückliegende Zeitraum (in Tagen) '
                    . 'für die Ermittlung der Bestseller berücksichtigt werden soll.',
            ]
        );

        $ins            = new stdClass();
        $ins->name      = 'manuell@' . \date('Y-m-d H:i:s');
        $ins->frequency = 24;
        $ins->jobType   = Type::TOPSELLER;
        $ins->startTime = '01:00:00';
        $ins->startDate = (new DateTime())->format('Y-m-d H:i:s');
        $this->db->insert('tcron', $ins);
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $crons = $this->db->getObjects(
            'SELECT cronID FROM tcron WHERE jobType = :tp',
            ['tp' => Type::TOPSELLER]
        );
        foreach ($crons as $cron) {
            $this->db->delete('tjobqueue', 'cronID', (int)$cron->cronID);
            $this->db->delete('tcron', 'cronID', (int)$cron->cronID);
        }

        $this->removeConfig('global_bestseller_tage');
        if ($this->fetchOne("SHOW INDEX FROM tbestellung WHERE KEY_NAME = 'idx_dErstellt_WK'")) {
            $this->execute('DROP INDEX idx_dErstellt_WK ON tbestellung');
        }
    }
}
