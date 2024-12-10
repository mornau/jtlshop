<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210226104800
 */
class Migration20210226104800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add product matrix lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'productDetails', 'productMatrixTitle', 'Warenkorbmatrix');
        $this->setLocalization('eng', 'productDetails', 'productMatrixTitle', 'Basket matrix');
        $this->setLocalization('ger', 'productDetails', 'productMatrixDesc', '');
        $this->setLocalization('eng', 'productDetails', 'productMatrixDesc', '');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('productMatrixTitle', 'productDetails');
        $this->removeLocalization('productMatrixDesc', 'productDetails');
    }
}
