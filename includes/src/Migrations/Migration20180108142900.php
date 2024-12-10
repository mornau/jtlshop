<?php

/**
 * Remove caching method "mysql"
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180108142900
 */
class Migration20180108142900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Remove caching method mysql';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("DELETE FROM `teinstellungenconfwerte` WHERE kEinstellungenConf = 1551 AND cWert = 'mysql'");
        $this->execute("UPDATE `teinstellungen` SET `cWert`='null' WHERE `cWert`='mysql' AND cName = 'caching_method'");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "INSERT INTO `teinstellungenconfwerte` (kEinstellungenConf, cName, cWert, nSort)
                VALUES (1551, 'MySQL', 'mysql', 9)"
        );
    }
}
