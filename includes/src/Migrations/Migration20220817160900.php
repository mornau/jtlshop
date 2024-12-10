<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220817160900
 */
class Migration20220817160900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Parameter handling for redirects';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tredirect` ADD COLUMN `paramHandling` TINYINT(1) NOT NULL DEFAULT 0');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tredirect` DROP COLUMN `paramHandling`');
    }
}
