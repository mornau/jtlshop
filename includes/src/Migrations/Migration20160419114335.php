<?php

/**
 * removed bilder_hochskalieren
 *
 * @author aj
 * @created Tue, 19 Apr 2016 11:43:35 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160419114335
 */
class Migration20160419114335 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'aj';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('bilder_hochskalieren');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
