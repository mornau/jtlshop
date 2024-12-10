<?php

/**
 * create columns for dynamic options sources
 *
 * @author fm
 * @created Fri, 20 May 2016 14:21:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160520142100
 */
class Migration20160520142100 extends Migration implements IMigration
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
        $this->execute(
            'ALTER TABLE `tplugineinstellungenconf`
                ADD COLUMN `cSourceFile` VARCHAR(255) NULL DEFAULT NULL'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tplugineinstellungenconf` DROP COLUMN `cSourceFile`');
    }
}
