<?php

/**
 * Add portlet lang vars
 *
 * @author mh
 * @created Thu, 4 July 2019 09:27:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190704092700
 */
class Migration20190704092700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add portlet lang vars';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'days', 'Tage');
        $this->setLocalization('eng', 'global', 'days', 'Days');
        $this->setLocalization('ger', 'global', 'hours', 'Stunden');
        $this->setLocalization('eng', 'global', 'hours', 'Hours');
        $this->setLocalization('ger', 'global', 'minutes', 'Minuten');
        $this->setLocalization('eng', 'global', 'minutes', 'Minutes');
        $this->setLocalization('ger', 'global', 'seconds', 'Sekunden');
        $this->setLocalization('eng', 'global', 'seconds', 'Seconds');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('days');
        $this->removeLocalization('hours');
        $this->removeLocalization('minutes');
        $this->removeLocalization('seconds');
    }
}
