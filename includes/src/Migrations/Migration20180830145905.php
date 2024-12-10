<?php

/**
 * Update evo template version into semantic version
 *
 * @author msc
 * @created Thu, 30 Aug 2018 14:59:05 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180830145905
 */
class Migration20180830145905 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    public function getDescription(): string
    {
        return 'Update evo template version into semantic version';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("UPDATE `ttemplate` SET `version` = '5.0.0' WHERE `cTemplate` = 'Evo' AND `eTyp` = 'standard'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE `ttemplate` SET `version` = '5.0' WHERE `cTemplate` = 'Evo' AND `eTyp` = 'standard'");
    }
}
