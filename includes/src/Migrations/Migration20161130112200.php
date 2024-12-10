<?php

/**
 * adds free gift error message
 *
 * @author ms
 * @created Wed, 30 Nov 2016 11:22:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161130112200
 */
class Migration20161130112200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'errorMessages',
            'freegiftsMinimum',
            'Der Gratisartikel-Mindestbestellwert ist nicht erreicht.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'freegiftsMinimum',
            'Minimum shopping cart value not reached for this free gift.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('freegiftsMinimum');
    }
}
