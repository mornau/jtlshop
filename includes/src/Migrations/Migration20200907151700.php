<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200907151700
 */
class Migration20200907151700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Revert newsletter unsubscribeAnytime lang';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'newsletter',
            'unsubscribeAnytime',
            'Abonnieren Sie jetzt den Newsletter und verpassen Sie keine Angebote. Die Abmeldung ist jederzeit möglich.'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'unsubscribeAnytime',
            'Subscribe to the newsletter now and never miss the latest offers again! You can unsubscribe at any time.'
        );

        $this->setLocalization(
            'ger',
            'newsletter',
            'newsletterInformedConsent',
            'Bitte senden Sie mir entsprechend Ihrer <a href="%s" target="_blank">Datenschutzerklärung</a> '
            . 'regelmäßig und  jederzeit widerruflich Informationen zu Ihrem Produktsortiment per E-Mail zu.'
        );
        $this->setLocalization(
            'eng',
            'newsletter',
            'newsletterInformedConsent',
            'Please email me the latest information on your product portfolio regularly '
            . 'and in accordance with your data <a href="%s" target="_blank">privacy notice</a>. '
            . 'I recognise that I can revoke my permission to receive said emails at any time.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('newsletterInformedConsent', 'newsletter');
    }
}
