<?php

/**
 * Increase text fiels length for currencies
 *
 * @author fm
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190207074500
 */
class Migration20190207074500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Increase currency table text fields length';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `twaehrung` 
            CHANGE COLUMN `cName` `cName` VARCHAR(255) NULL DEFAULT NULL,
            CHANGE COLUMN `cNameHTML` `cNameHTML` VARCHAR(255) NULL DEFAULT NULL'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE `twaehrung` 
            CHANGE COLUMN `cName` `cName` VARCHAR(20) NULL DEFAULT NULL,
            CHANGE COLUMN `cNameHTML` `cNameHTML` VARCHAR(20) NULL DEFAULT NULL'
        );
    }
}
