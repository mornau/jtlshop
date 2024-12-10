<?php

/**
 * changes optional fill out hint
 *
 * @author ms
 * @created Tue, 04 Sep 2018 11:48:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20180904114800
 */
class Migration20180904114800 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Change optional fill out hint';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeLocalization('conditionalFillOut');

        $this->setLocalization('ger', 'global', 'optional', 'optionale Angabe');
        $this->setLocalization('eng', 'global', 'optional', 'optional');

        $this->removeLocalization('yourDataDesc');
        $this->removeLocalization('kwkNameDesc');
        $this->removeLocalization('fillOutNotification');
        $this->removeLocalization('fillOutQuestion');
        $this->removeLocalization('bewertungWrongdata');
        $this->removeLocalization('rma_error_required');

        $this->setLocalization(
            'ger',
            'errorMessages',
            'mandatoryFieldNotification',
            'Bitte füllen Sie alle Pflichtfelder aus.'
        );
        $this->setLocalization(
            'eng',
            'errorMessages',
            'mandatoryFieldNotification',
            'Please complete all required fields.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('optional');

        $this->setLocalization('ger', 'checkout', 'conditionalFillOut', 'optionale Angabe');
        $this->setLocalization('eng', 'checkout', 'conditionalFillOut', 'conditional fill in');

        $this->removeLocalization('mandatoryFieldNotification');

        $this->setLocalization('ger', 'account data', 'yourDataDesc', 'Felder mit einem * müssen ausgefüllt werden.');
        $this->setLocalization('eng', 'account data', 'yourDataDesc', 'Fields with a * must be filled in.');

        $this->setLocalization('ger', 'login', 'kwkNameDesc', 'Füllen Sie alle Felder aus');
        $this->setLocalization('eng', 'login', 'kwkNameDesc', 'Fill in all fields');

        $this->setLocalization(
            'ger',
            'messages',
            'fillOutNotification',
            'Bitte füllen Sie alle notwendigen Felder der Verfügbarkeitsanfrage aus!'
        );
        $this->setLocalization(
            'eng',
            'messages',
            'fillOutNotification',
            'Please fill out all necessary fields in the availbility notification!'
        );

        $this->setLocalization('ger', 'messages', 'fillOutQuestion', 'Bitte füllen Sie alle notwendigen Felder aus!');
        $this->setLocalization(
            'eng',
            'messages',
            'fillOutQuestion',
            'Please fill out all necessary fields in your question!'
        );

        $this->setLocalization(
            'ger',
            'errorMessages',
            'bewertungWrongdata',
            'Bitte füllen Sie alle erforderlichen Felder der Bewertung aus.'
        );
        $this->setLocalization('eng', 'errorMessages', 'bewertungWrongdata', 'Error: please fill out all the fields.');

        $this->setLocalization('ger', 'rma', 'rma_error_required', 'Bitte füllen Sie alle Pflichtfelder aus');
        $this->setLocalization('eng', 'rma', 'rma_error_required', 'Please fill out all required fields');
    }
}
