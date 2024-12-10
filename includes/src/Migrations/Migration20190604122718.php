<?php

/**
 * cleanup newsletter optin messages
 *
 * @author cr
 * @created Tue, 04 Jun 2019 12:27:18 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190604122718
 */
class Migration20190604122718 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Cleanup newsletter optin messages';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeLocalization('newsletterExists');
        $this->removeLocalization('newsletterDelete');

        $this->setLocalization(
            'ger',
            'messages',
            'optinSucceededMailSent',
            'Die Mail mit Ihrem Freischalt-Code wurde bereits an Sie verschickt'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'optinSucceededMailSent',
            'The mail with your activation-code was already sent.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('optinSucceededMailSent');

        $this->setLocalization(
            'ger',
            'errorMessages',
            'newsletterDelete',
            'Sie wurden erfolgreich aus unserem Newsletterverteiler ausgetragen.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newsletterDelete',
            'You have been successfully deleted from our News list.'
        );
        $this->setLocalization(
            'ger',
            'errorMessages',
            'newsletterExists',
            'Fehler: Ihre E-Mail-Adresse ist bereits vorhanden.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'newsletterExists',
            'Error: It appears that your E-Mail already exists.'
        );
    }
}
