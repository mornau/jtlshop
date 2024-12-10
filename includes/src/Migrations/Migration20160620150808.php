<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160620150808
 */
class Migration20160620150808 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            "CREATE TABLE IF NOT EXISTS `tadminloginattribut` (
                `kAttribut`    INT          NOT NULL AUTO_INCREMENT,
                `kAdminlogin`  INT          NOT NULL,
                `cName`        VARCHAR(45)  NOT NULL,
                `cAttribValue` VARCHAR(512) NOT NULL DEFAULT '',
                `cAttribText`  TEXT             NULL,
                PRIMARY KEY (`kAttribut`),
                UNIQUE INDEX `cName_UNIQUE` (`kAdminlogin`, `cName`)) 
                ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );

        $this->execute(
            'CREATE TABLE IF NOT EXISTS `tcontentauthor` (
                `kContentAuthor`  INT          NOT NULL AUTO_INCREMENT,
                `cRealm`          VARCHAR(45)  NOT NULL,
                `kAdminlogin`     INT          NOT NULL,
                `kContentId`      INT          NOT NULL,
                PRIMARY KEY (`kContentAuthor`),
                UNIQUE INDEX `cRealm_UNIQUE` (`cRealm`, `kContentId`)) 
                ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS `tcontentauthor`');
        $this->execute('DROP TABLE IF EXISTS `tadminloginattribut`');
    }
}
