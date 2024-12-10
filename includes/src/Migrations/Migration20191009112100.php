<?php

/**
 * Add nova lang vars
 *
 * @author mh
 * @created Wed, 9 Oct 2019 11:21:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20191009112100
 */
class Migration20191009112100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add nova lang vars';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'global', 'selectChoose', 'Auswählen');
        $this->setLocalization('eng', 'global', 'selectChoose', 'Choose');
        $this->setLocalization('ger', 'global', 'warehouseAvailability', 'Bestand pro Lager anzeigen');
        $this->setLocalization('eng', 'global', 'warehouseAvailability', 'Show stock level per warehouse');
        $this->setLocalization('ger', 'global', 'warehouse', 'Lager');
        $this->setLocalization('eng', 'global', 'warehouse', 'Warehouse');
        $this->setLocalization('ger', 'global', 'status', 'Status');
        $this->setLocalization('eng', 'global', 'status', 'Status');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('selectChoose');
        $this->removeLocalization('warehouseAvailability');
        $this->removeLocalization('status');
        $this->removeLocalization('warehouse');
    }
}
