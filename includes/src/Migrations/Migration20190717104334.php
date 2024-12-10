<?php

/**
 * Add lang var for status details
 *
 * @author fp
 * @created Wed, 17 Jul 2019 10:43:34 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190717104334
 */
class Migration20190717104334 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Add lang var for status details';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'account data',
            'enter_plz_for_details',
            'Bitte geben Sie die bei der Bestellung verwendete PLZ der Rechnungsadresse ein, '
            . 'um die Bestelldetails anzuzeigen.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'enter_plz_for_details',
            'Please enter the postcode for the billing address used in the order to view the order details.'
        );

        $this->execute('ALTER TABLE tbestellstatus ADD COLUMN failedAttempts INT NOT NULL DEFAULT 0');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tbestellstatus DROP COLUMN failedAttempts');
        $this->removeLocalization('enter_plz_for_details');
    }
}
