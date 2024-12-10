<?php

/**
 * Changes salutions
 *
 * @author ms
 * @created Wed, 15 Feb 2017 16:18:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170215161800
 */
class Migration20170215161800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Change female salutation to ms and adds general salutation';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('eng', 'global', 'salutationW', 'Ms');

        $this->setLocalization('ger', 'global', 'salutationGeneral', 'Frau/Herr');
        $this->setLocalization('eng', 'global', 'salutationGeneral', 'Ms/Mr');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('eng', 'global', 'salutationW', 'Mrs');

        $this->removeLocalization('salutationGeneral');
    }
}
