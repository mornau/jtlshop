<?php

/**
 * adds updating stock lang key
 *
 * @author ms
 * @created Wed, 10 May 2017 09:19:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20170510091900
 */
class Migration20170510091900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add updating stock lang key';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'productDetails',
            'updatingStockInformation',
            'Lagerinformationen für Variationen werden geladen'
        );
        $this->setLocalization('eng', 'productDetails', 'updatingStockInformation', 'updating stock information');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('updatingStockInformation');
    }
}
