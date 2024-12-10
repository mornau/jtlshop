<?php

/**
 * Add file upload permission
 *
 * @author mh
 * @created Mon, 20 Apr 2020 12:22:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200420122000
 */
class Migration20200420122000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add file upload permission';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("INSERT INTO `tadminrecht` VALUES ('IMAGE_UPLOAD', 'File upload');");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tadminrecht` WHERE `cRecht`='IMAGE_UPLOAD';");
    }
}
