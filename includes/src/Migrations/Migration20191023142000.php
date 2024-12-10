<?php

/**
 * add lang vars for nova menu
 *
 * @author mh
 * @created Wed, 23 Oct 2019 14:20:00 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191023142000
 */
class Migration20191023142000 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add lang vars for nova menu';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'menuShow', '%s anzeigen');
        $this->setLocalization('eng', 'global', 'menuShow', 'Show %s');
        $this->setLocalization('ger', 'global', 'menuName', 'Menü');
        $this->setLocalization('eng', 'global', 'menuName', 'Menu');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('menuShow');
        $this->removeLocalization('menuName');
    }
}
