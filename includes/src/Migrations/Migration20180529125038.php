<?php

/**
 * Add LastArticleID to texportqueue
 *
 * @author fp
 * @created Tue, 29 May 2018 12:50:38 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180529125038
 */
class Migration20180529125038 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Add LastArticleID to texportqueue';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `texportqueue` ADD COLUMN `nLastArticleID` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `nLimit_m`'
        );
        $this->execute(
            'ALTER TABLE `tjobqueue` ADD COLUMN `nLastArticleID` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `nLimitm`'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE `texportqueue` DROP COLUMN `nLastArticleID`'
        );
        $this->execute(
            'ALTER TABLE `tjobqueue` DROP COLUMN `nLastArticleID`'
        );
    }
}
