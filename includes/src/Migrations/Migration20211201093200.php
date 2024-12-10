<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20211201093200
 */
class Migration20211201093200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add min value info lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'productDetails',
            'minValueInfo',
            'Bitte beachten Sie den Mindestbestellwert von %s %s.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'minValueInfo',
            'Please note our minimum order value of %2$s %1$s.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('minValueInfo', 'productDetails');
    }
}
