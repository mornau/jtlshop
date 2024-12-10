<?php

/**
 * fix msg-id spelling for optin
 *
 * @author cr
 * @created Thu, 09 May 2019 12:12:04 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Migration
 */
class Migration20190509121204 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Fix msg-id spelling for optin';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeLocalization('optinSuccededAgain');
        $this->removeLocalization('optinSucceded');

        $this->setLocalization('ger', 'messages', 'optinSucceeded', 'Ihre Freischaltung ist erfolgt.');
        $this->setLocalization('eng', 'messages', 'optinSucceeded', 'Your confirmation was successfull.');
        $this->setLocalization('ger', 'messages', 'optinSucceededAgain', 'Ihre Freischaltung ist bereits erfolgt.');
        $this->setLocalization('eng', 'messages', 'optinSucceededAgain', 'Your confirmation is already active.');

        $this->removeLocalization('availAgainOptinCreated');
        $this->setLocalization(
            'ger',
            'messages',
            'availAgainOptinCreated',
            'Vielen Dank, Ihre Daten haben wir erhalten. Wir haben Ihnen eine E-Mail
            mit einem Freischaltcode zugeschickt.
            Bitte klicken Sie auf diesen Link in der E-Mail,
            um informiert zu werden, sobald der Artikel wieder verfügbar ist.'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'availAgainOptinCreated',
            'Thank you very much. We have sent you a e-mail with a confirmation-code.
            Please click the link in this e-mail to be informed if the product
            is available again.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('optinSucceeded');
        $this->removeLocalization('optinSucceededAgain');

        $this->setLocalization('ger', 'messages', 'optinSucceded', 'Ihre Freischaltung ist erfolgt.');
        $this->setLocalization('eng', 'messages', 'optinSucceded', 'Your confirmation was successfull.');
        $this->setLocalization('ger', 'messages', 'optinSuccededAgain', 'Ihre Freischaltung ist bereits erfolgt.');
        $this->setLocalization('eng', 'messages', 'optinSuccededAgain', 'Your confirmation is already active.');
    }
}
