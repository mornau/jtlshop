<?php

/**
 * add_comparelist_rows
 *
 * @author mh
 * @created Tue, 12 Mar 2019 11:22:01 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190312112201
 */
class Migration20190312112201 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add comparelist rows';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig('vergleichsliste_verfuegbarkeit', 7, 106, 'Anzeigepriorität Verfügbarkeit', 'number', 113);
        $this->setConfig('vergleichsliste_lieferzeit', 6, 106, 'Anzeigepriorität Lieferzeit', 'number', 116);

        $this->setLocalization('ger', 'global', 'showNone', 'Alle ausblenden');
        $this->setLocalization('eng', 'global', 'showNone', 'Show none');
        $this->setLocalization('ger', 'global', 'filter', 'Filter');
        $this->setLocalization('eng', 'global', 'filter', 'Filter');
        $this->setLocalization(
            'ger',
            'comparelist',
            'productNumberHint',
            'Bitte mindestens zwei Artikel für einen Vergleich hinzufügen.'
        );
        $this->setLocalization(
            'eng',
            'comparelist',
            'productNumberHint',
            'Please add at least two products to compare.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('vergleichsliste_verfuegbarkeit');
        $this->removeConfig('vergleichsliste_lieferzeit');

        $this->removeLocalization('showNone');
        $this->removeLocalization('filter');
        $this->removeLocalization('productNumberHint');
    }
}
