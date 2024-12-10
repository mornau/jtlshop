<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20210525130500
 */
class Migration20210525130500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Remove comparelist row setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('vergleichsliste_spaltengroesseattribut');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->setConfig(
            'vergleichsliste_spaltengroesseattribut',
            '300',
            \CONF_VERGLEICHSLISTE,
            'Spaltenbreite der Attribute',
            'number',
            210
        );
    }
}
