<?php

/**
 * Add language variables for missing tax zone
 *
 * @author fp
 * @created Tue, 10 Oct 2017 16:06:27 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20171010160627
 */
class Migration20171010160627 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Add language variables for missing tax zone';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'errorMessages',
            'missingTaxZoneForDeliveryCountry',
            'Ein Versand nach %s ist aktuell nicht m&ouml;glich, da keine g&uuml;ltige Steuerzone hinterlegt ist.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'missingTaxZoneForDeliveryCountry',
            'A shipment to %s is currently not possible because there is no assigned tax zone.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('missingTaxZoneForDeliveryCountry');
    }
}
