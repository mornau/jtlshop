<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20201014094600
 */
class Migration20201014094600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add configurator hint lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'productDetails',
            'completeConfigGroupHint',
            'Bitte wählen Sie zuerst Ihre gewünschten Komponenten in der aktuellen Gruppe aus. '
            . 'Klicken Sie dann auf „Nächste Konfigurationsgruppe“.'
        );
        $this->setLocalization(
            'eng',
            'productDetails',
            'completeConfigGroupHint',
            'Please select the desired components in the current group first. Then click \"Next configuration group\".'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('completeConfigGroupHint', 'productDetails');
    }
}
