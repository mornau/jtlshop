<?php

/**
 * add plugin state log table
 *
 * @author dr
 * @created Tue, 26 Mar 2024 09:40:50 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240326094050
 */
class Migration20240326094050 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'add plugin state log table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE plugin_state_log (
                id           INT NOT NULL AUTO_INCREMENT,
                adminloginID INT NOT NULL,
                pluginID     INT NOT NULL,
                pluginName   VARCHAR(255) NOT NULL,
                stateOld     TINYINT UNSIGNED,
                stateNew     TINYINT UNSIGNED,
                timestamp    DATETIME NOT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE plugin_state_log');
    }
}
