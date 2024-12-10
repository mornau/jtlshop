<?php

/**
 * lang_for_new_alerts
 *
 * @author mh
 * @created Thu, 17 Jan 2019 09:31:18 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190117093118
 */
class Migration20190117093118 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Language vars for new alerts';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'errorMessages', 'statusOrderNotFound', 'Keine passende Bestellung gefunden.');
        $this->setLocalization('eng', 'errorMessages', 'statusOrderNotFound', 'No matching order found.');

        $this->setLocalization('ger', 'errorMessages', 'uidNotFound', 'Keine uid gefunden.');
        $this->setLocalization('eng', 'errorMessages', 'uidNotFound', 'Uid not found.');

        $this->setLocalization('ger', 'messages', 'accountDeleted', 'Kundenkonto erfolgreich gelöscht.');
        $this->setLocalization('eng', 'messages', 'accountDeleted', 'Account successfully deleted.');

        $this->setLocalization(
            'ger',
            'errorMessages',
            'cartPersRemoved',
            'Der Artikel "%s" konnte nicht in den Warenkorb übernommen werden.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'cartPersRemoved',
            'The product "%s" could not be added to the cart.'
        );

        $this->setLocalization(
            'ger',
            'messages',
            'continueAfterActivation',
            'Sie können mit dem Bestellprozess fortfahren wenn Ihr Kundenkonto freigeschaltet wurde.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'continueAfterActivation',
            'You can continue with your order after your account has been activated.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('statusOrderNotFound');
        $this->removeLocalization('uidNotFound');
        $this->removeLocalization('accountDeleted');
        $this->removeLocalization('cartPersRemoved');
        $this->removeLocalization('continueAfterActivation');
    }
}
