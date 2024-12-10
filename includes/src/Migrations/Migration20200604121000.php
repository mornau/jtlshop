<?php

/**
 * @author mh
 * @created Tue, 4 Jun 2020 12:10:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200604121000
 */
class Migration20200604121000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove tag configgroup';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('configgroup_110_tag_filter');
        $this->removeConfig('configgroup_8_box_tagcloud');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
