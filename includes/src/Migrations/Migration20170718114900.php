<?php

/**
 * adds cIgnoreShippingProposal to tversandart
 *
 * @author ms
 * @created Tue, 18 Jul 2017 11:49:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170718114900
 */
class Migration20170718114900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add cIgnoreShippingProposal to tversandart';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "ALTER TABLE tversandart
            ADD COLUMN cIgnoreShippingProposal CHAR(1) NOT NULL DEFAULT 'N' AFTER cSendConfirmationMail;"
        );

        $this->execute(
            "UPDATE tversandartzahlungsart AS vz
                    JOIN tzahlungsart AS z ON 
                        vz.kZahlungsart = z.kZahlungsart
                    JOIN tversandart AS v ON 
                        vz.kVersandart = v.kVersandart SET v.cIgnoreShippingProposal='Y'
                WHERE v.cName LIKE'%Abholung%' 
                    OR z.cTSCode = 'CASH_ON_PICKUP' 
                        AND	(SELECT 
                            COUNT(nvz.kZahlungsart) 
                            FROM tversandartzahlungsart AS nvz 
                            WHERE nvz.kVersandart = v.kVersandart) = 1;"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tversandart DROP COLUMN cIgnoreShippingProposal');
    }
}
