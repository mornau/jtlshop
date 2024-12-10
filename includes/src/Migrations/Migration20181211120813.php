<?php

/**
 * overlays_template_specific
 *
 * @author mh
 * @created Tue, 11 Dec 2018 12:08:13 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181211120813
 */
class Migration20181211120813 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Make overlays template specific';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `tsuchspecialoverlaysprache`
                          ADD COLUMN `cTemplate` VARCHAR(255) NOT NULL AFTER `kSprache`,
                          DROP PRIMARY KEY,
                          ADD PRIMARY KEY (`kSuchspecialOverlay`, `kSprache`, `cTemplate`)'
        );
        $this->execute("UPDATE `tsuchspecialoverlaysprache` SET `cTemplate` = 'default'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tsuchspecialoverlaysprache` WHERE `cTemplate` != 'default'");
        $this->execute(
            'ALTER TABLE `tsuchspecialoverlaysprache`
                           DROP COLUMN `cTemplate`,
                           DROP PRIMARY KEY,
                           ADD PRIMARY KEY (`kSuchspecialOverlay`, `kSprache`)'
        );
    }
}
