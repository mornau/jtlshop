<?php

/**
 * add_filter_lang
 *
 * @author mh
 * @created Wed, 15 May 2019 15:09:02 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190515150902
 */
class Migration20190515150902 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add filter lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'global',
            'noFilterResults',
            'Für die Filterung wurden keine Ergebnisse gefunden.'
        );
        $this->setLocalization('eng', 'global', 'noFilterResults', 'No results found for this filter.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('noFilterResults');
    }
}
