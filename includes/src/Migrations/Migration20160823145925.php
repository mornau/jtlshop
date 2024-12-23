<?php

/**
 * Add localized message for mutating basket
 *
 * @author root
 * @created Tue, 23 Aug 2016 14:59:25 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160823145925
 */
class Migration20160823145925 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'checkout',
            'yourbasketismutating',
            'Ihr Warenkorb wurde aufgrund von Preis- oder Lagerbestandsänderungen aktualisiert. '
            . 'Bitte prüfen Sie die Warenkorbpositionen.'
        );
        $this->setLocalization(
            'eng',
            'checkout',
            'yourbasketismutating',
            'Your shopping cart has been updated due to price or stock changes. Please check your order items.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM tsprachwerte WHERE cName = 'yourbasketismutating'");
    }
}
