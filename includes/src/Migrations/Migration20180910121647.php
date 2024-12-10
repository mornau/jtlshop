<?php

/**
 * correct_lang_var_product_available
 *
 * @author mh
 * @created Mon, 10 Sep 2018 12:16:47 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180910121647
 */
class Migration20180910121647 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Correct lang var productAvailable';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'productAvailable', 'verfügbar');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'global', 'productAvailable', 'Artikel verfügbar ab');
    }
}
