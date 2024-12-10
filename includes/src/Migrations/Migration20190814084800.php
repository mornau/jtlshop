<?php

/**
 * Add aria labels
 *
 * @author ms
 * @created Wed, 14 Aug 2019 08:48:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190814084800
 */
class Migration20190814084800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add aria language vars';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'aria', 'visit_us_on', 'Besuchen Sie uns auch auf %s');
        $this->setLocalization('eng', 'aria', 'visit_us_on', 'visit us on %s');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('visit_us_on');
    }
}
