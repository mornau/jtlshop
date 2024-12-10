<?php

/**
 * Create table for user depended category discount
 *
 * @author fp
 * @created Wed, 05 Jul 2023 12:32:41 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230705123241
 */
class Migration20230705123241 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'New table for user dependent category discount';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE `category_customerdiscount` (
                id          INT     NOT NULL AUTO_INCREMENT PRIMARY KEY,
                customerId  INT     NOT NULL,
                categoryId  INT     NOT NULL,
                discount    FLOAT   NOT NULL DEFAULT 0,
                UNIQUE KEY `idx_category_customer_uq` (customerId, categoryId)
            ) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'DROP TABLE IF EXISTS `category_customerdiscount`'
        );
    }
}
