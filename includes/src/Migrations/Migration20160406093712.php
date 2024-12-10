<?php

/**
 * news language setting
 *
 * @author ms
 * @created Wed, 06 Apr 2016 09:37:12 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160406093712
 */
class Migration20160406093712 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'news', 'newsRestricted', 'Dieser Beitrag unterliegt Beschr&auml;nkungen.');
        $this->setLocalization('eng', 'news', 'newsRestricted', 'This post is subject to restrictions.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tsprachwerte` WHERE `kSprachsektion` = 14 AND `cName` = 'newsRestricted';");
    }
}
