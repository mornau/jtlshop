<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240418102947
 */
class Migration20240418102947 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'add template settings log table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE template_settings_log (
                id           INT NOT NULL AUTO_INCREMENT,
                adminloginID INT NOT NULL,
                sectionID    VARCHAR(255) NOT NULL,
                settingID    VARCHAR(255) NOT NULL,
                settingName  MEDIUMTEXT,
                valueOld     MEDIUMTEXT,
                valueNew     MEDIUMTEXT,
                valueNameOld MEDIUMTEXT,
                valueNameNew MEDIUMTEXT,
                valueDiff    MEDIUMTEXT,
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
        $this->execute('DROP TABLE template_settings_log');
    }
}
