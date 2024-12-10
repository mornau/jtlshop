<?php

/**
 * enable_semantic_versioning
 *
 * @author mh
 * @created Wed, 01 Aug 2018 12:41:35 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180801124135
 */
class Migration20180801124135 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Enable semantic versioning for templates';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "UPDATE `ttemplate` SET
                `cTemplate` = 'bootstrap',
                `eTyp`      = 'admin',
                `parent`    = NULL,
                `name`      = 'bootstrap',
                `author`    = 'JTL-Software-GmbH',
                `url`       = 'https://www.jtl-software.de',
                `version`   = 1.0,
                `preview`   = 'preview.png'
                WHERE `cTemplate` = 'bootstrap' AND `eTyp` = 'admin'"
        );
        $this->execute('UPDATE `ttemplate` SET `version` = 1.0 WHERE `version` IS NULL');
        $this->execute('ALTER TABLE `ttemplate` CHANGE COLUMN `version` `version` VARCHAR(20) NOT NULL');
        $this->execute(
            "UPDATE `ttemplate`
                SET `version` = CONCAT(`version`, IF(LOCATE('.', `version`) = 0, '.0.0', '.0'))"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("UPDATE `ttemplate` SET `version` = SUBSTRING_INDEX(`version`, '.', 2)");
        $this->execute('ALTER TABLE ttemplate CHANGE COLUMN version version FLOAT NOT NULL');
    }
}
