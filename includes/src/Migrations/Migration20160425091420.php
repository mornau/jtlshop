<?php

/**
 * change_of_the_language_variable_dimensions
 *
 * @author msc
 * @created Mon, 25 Apr 2016 09:14:20 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160425091420
 */
class Migration20160425091420 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'productDetails', 'dimensions', 'Abmessungen(LxBxH)');
        $this->setLocalization('eng', 'productDetails', 'dimensions', 'Dimensions(LxWxH)');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setLocalization('ger', 'productDetails', 'dimensions', 'Abmessungen');
        $this->setLocalization('eng', 'productDetails', 'dimensions', 'Dimensions');
    }
}
