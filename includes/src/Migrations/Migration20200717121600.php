<?php

/**
 * Add canary islands
 *
 * @author mh
 * @created Fr, 17 July 2020 12:16:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200717121600
 */
class Migration20200717121600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add canary islands';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $isSet = $this->getDB()->getSingleObject("SELECT `cISO` FROM `tland` WHERE cISO = 'IC'");
        if ($isSet === null) {
            $this->execute("INSERT INTO `tland` VALUES ('IC', 'Kanarische Inseln', 'Canary Islands', 1, 'Europa')");
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tland` WHERE cISO = 'IC'");
    }
}
