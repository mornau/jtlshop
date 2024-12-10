<?php

/**
 * Add OPC custom input table
 *
 * @author dr
 * @created Fri, 13 Oct 2023 15:02:46 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20231013150246
 */
class Migration20231013150246 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add OPC custom input table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE portlet_input_type (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                plugin_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY name_index (name)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE portlet_input_type');
    }
}
