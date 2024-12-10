<?php

/**
 * @author fm
 * @created Mon, 05 Nov 2018 11:09:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181105110900
 */
class Migration20181105110900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Longer slider/slide titles';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `tslide` CHANGE COLUMN `cTitel` `cTitel` VARCHAR(255) NOT NULL');
        $this->execute('ALTER TABLE `tslider` CHANGE COLUMN `cName` `cName` VARCHAR(255) NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `tslide` CHANGE COLUMN `cTitel` `cTitel` VARCHAR(45) NOT NULL');
        $this->execute('ALTER TABLE `tslider` CHANGE COLUMN `cName` `cName` VARCHAR(45) NOT NULL');
    }
}
