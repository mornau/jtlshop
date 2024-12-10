<?php

/**
 * Add new language variable called 'less' used mainly for bootstrap collapse elements.
 *
 * @author timniko
 * @created Tue, 02 May 2023 13:17:55 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230502131755
 */
class Migration20230502131755 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'tnt';
    }

    public function getDescription(): string
    {
        return 'Add new language variable called less used '
            . 'mainly for bootstrap collapse elements.';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'less', 'weniger');
        $this->setLocalization('eng', 'global', 'less', 'less');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tsprachwerte` WHERE `kSprachsektion` = 1 AND `cName` = 'less';");
    }
}
