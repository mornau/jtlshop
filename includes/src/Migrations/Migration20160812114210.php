<?php

/**
 * add_language_variable_descriptionview
 *
 * @author msc
 * @created Fri, 12 Aug 2016 11:42:10 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160812114210
 */
class Migration20160812114210 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'msc';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'showDescription', 'Beschreibung anzeigen');
        $this->setLocalization('eng', 'global', 'showDescription', 'Show description');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('showDescription');
    }
}
