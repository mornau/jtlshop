<?php

/**
 * @author fm
 * @created Thu, 30 Oct 2018 11:06:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181030110600
 */
class Migration20181030110600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Add admin login lock';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tadminlogin` ADD COLUMN `locked_at` DATETIME DEFAULT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tadminlogin` DROP COLUMN `locked_at`');
    }
}
