<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210125155700
 */
class Migration20210125155700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add comparelist lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'comparelist', 'showLabels', 'Labels anzeigen');
        $this->setLocalization('ger', 'comparelist', 'hideLabels', 'Labels verstecken');
        $this->setLocalization('eng', 'comparelist', 'showLabels', 'Show labels');
        $this->setLocalization('eng', 'comparelist', 'hideLabels', 'Hide labels');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('showLabels', 'comparelist');
        $this->removeLocalization('hideLabels', 'comparelist');
    }
}
