<?php

/**
 * cLogin in tadmin should allow names longer then 20 characters
 *
 * @author fm
 * @created Tue, 12 Apr 2016 09:41:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160412094100
 */
class Migration20160412094100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tadminlogin` CHANGE COLUMN `cLogin` `cLogin` VARCHAR(255) NULL DEFAULT NULL;');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tadminlogin` CHANGE COLUMN `cLogin` `cLogin` VARCHAR(20) NULL DEFAULT NULL;');
    }
}
