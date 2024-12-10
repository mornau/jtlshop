<?php

/**
 * Add characters left message for inputs with max length
 *
 * @author Tim Niko Tegtmeyer
 * @created Mon, 26 Jun 2023 12:58:13 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230626125813
 */
class Migration20230626125813 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'Tim Niko Tegtmeyer';
    }

    public function getDescription(): string
    {
        return 'Add characters left message for inputs with max length';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'charactersLeft', 'Zeichen übrig');
        $this->setLocalization('eng', 'global', 'charactersLeft', 'characters left');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tsprachwerte` WHERE `kSprachsektion` = 1 AND `cName` = 'charactersLeft';");
    }
}
