<?php

/**
 * split cron intervals
 *
 * @author fm
 * @created Thu, 05 Jun 2018 12:20:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Helpers\Text;
use JTL\Update\IMigration;
use JTL\Update\Migration;
use stdClass;

/**
 * Class Migration20180705122000
 */
class Migration20180705122000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Split cron intervals';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $statusMail = $this->getDB()->getSingleObject('SELECT * FROM tstatusemail');
        $updates    = [];
        if ($statusMail !== null) {
            foreach (Text::parseSSKint($statusMail->cIntervall) as $interval) {
                $upd            = new stdClass();
                $upd->cEmail    = $statusMail->cEmail;
                $upd->nInterval = $interval;
                $upd->cInhalt   = $statusMail->cInhalt;
                $upd->nAktiv    = $statusMail->nAktiv;
                if ($interval === 1) {
                    $upd->dLastSent = $statusMail->dLetzterTagesVersand;
                } elseif ($interval === 7) {
                    $upd->dLastSent = $statusMail->dLetzterWochenVersand;
                } else {
                    $upd->dLastSent = $statusMail->dLetzterMonatsVersand;
                }
                $updates[] = $upd;
            }
        }
        $this->execute('TRUNCATE TABLE `tstatusemail`');
        $this->execute(
            'ALTER TABLE `tstatusemail` 
                DROP COLUMN `cIntervall`,
                DROP COLUMN `dLetzterTagesVersand`,
                DROP COLUMN `dLetzterWochenVersand`,
                DROP COLUMN `dLetzterMonatsVersand`,
                ADD COLUMN `nInterval` INT NOT NULL,
                ADD COLUMN `dLastSent` DATETIME NULL DEFAULT NULL,
                ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT,
                ADD PRIMARY KEY (`id`)'
        );
        foreach ($updates as $update) {
            $this->getDB()->insert('tstatusemail', $update);
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
