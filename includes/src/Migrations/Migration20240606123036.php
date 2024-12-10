<?php

declare(strict_types=1);

namespace JTL\Migrations;

use DateTime;
use Exception;
use JTL\Cron\Type;
use JTL\Update\IMigration;
use JTL\Update\Migration;
use stdClass;

/**
 * Class Migration20240606123036
 */
class Migration20240606123036 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create cron job for cross selling';
    }

    private function createCron(): void
    {
        $cron            = new stdClass();
        $cron->name      = Type::XSELLING;
        $cron->jobType   = Type::XSELLING;
        $cron->frequency = 24;
        $cron->startDate = (new DateTime())->format('Y-m-d H:i:s');
        $cron->startTime = '01:00:00';

        $cronID = $this->db->insertRow('tcron', $cron);

        $jobQueue            = new stdClass();
        $jobQueue->cronID    = $cronID;
        $jobQueue->jobType   = Type::XSELLING;
        $jobQueue->isRunning = 0;
        $jobQueue->startTime = (new DateTime())->format('Y-m-d ') . $cron->startTime;

        $this->db->insertRow('tjobqueue', $jobQueue);
    }

    private function dropCron(): void
    {
        $this->db->delete('tjobqueue', 'jobType', Type::XSELLING);
        $this->db->delete('tcron', 'jobType', Type::XSELLING);
    }

    /**
     * @throws Exception
     */
    private function addConfig(): void
    {
        $this->setConfig(
            'artikeldetails_xselling_combi_count',
            1,
            \CONF_ARTIKELDETAILS,
            'Ab welcher Anzahl sollen Kaufkombinationen berücksichtigt werden',
            'number',
            240,
            (object)[
                'cBeschreibung' => 'Ab welcher Anzahl sollen Kaufkombinationen berücksichtigt werden?',
            ],
        );
        $this->setConfig(
            'artikeldetails_xselling_combi_max',
            0,
            \CONF_ARTIKELDETAILS,
            'Wie viele Kombinationen sollen max. berücksichtigt werden',
            'selectbox',
            250,
            (object)[
                'cBeschreibung' => 'Wie viele Kombinationen sollen max. berücksichtigt werden',
                'inputOptions'  => [
                    '0'   => 'Alle',
                    '100' => 'Top 100',
                    '50'  => 'Top 50',
                    '20'  => 'Top 20',
                    '10'  => 'Top 10',
                    '-1'  => 'Wie unter "Anzahl Produkte anzeigen"',
                ],
            ],
        );
    }

    private function createTables(): void
    {
        $this->execute(
            'CREATE TABLE cross_selling_view LIKE txsellkauf'
        );
        $this->execute(
            'ALTER TABLE txsellkauf ADD INDEX idx_kArtikel_nAnzahl(kArtikel, nAnzahl DESC)'
        );
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function up(): void
    {
        $this->addConfig();
        $this->createTables();
        $this->createCron();
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->dropCron();
        $this->removeConfig('artikeldetails_xselling_combi_count');
        $this->removeConfig('artikeldetails_xselling_combi_max');

        $this->execute(
            'DROP TABLE IF EXISTS cross_selling_view'
        );
        $this->execute(
            'ALTER TABLE txsellkauf DROP INDEX idx_kArtikel_nAnzahl'
        );
    }
}
