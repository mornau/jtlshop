<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

class Migration20240910154600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Language fixes for two factor authentication';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setLocalization(
            'ger',
            'account data',
            'manageTwoFADesc',
            'Hier können Sie die 2-Faktor-Authentifizierung aktivieren, deaktivieren und bearbeiten.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'manageTwoFADesc',
            'Here you can enable, disable and manage 2-Factor-Authentication.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'twoFAEnableWarning',
            'Wenn Sie die 2-Faktor-Authentifizierung aktivieren, können Sie sich nur noch '
            . 'mithilfe Ihres Smartphones anmelden.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAEnableWarning',
            'Please note: If you enable two-factor authentication, you can only log in using your smartphone.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'twoFAAppWarning',
            'Für die 2-Faktor-Authentifizierung müssen Sie eine Authenticator App Ihrer Wahl '
            . 'auf Ihrem Smartphone installieren, z. B. die Google Authenticator App.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAAppWarning',
            'To be able to use the two-factor authentication, you must install an authenticator app '
            . 'of your choice on your smartphone, such as the Google Authenticator app.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'twoFAEmergencyCodesNotice',
            'Hinweis: Drucken Sie die Notfall-Codes direkt aus oder speichern Sie sie digital. '
            . 'Die Notfall-Codes können nur einmal in Ihrem Kundenkonto eingesehen und jeder Code kann nur '
            . 'einmal verwendet werden.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAEmergencyCodesNotice',
            'Please note: Print the emergency codes right away or save them digitally. '
            . 'The emergency codes can only be viewed once in your customer account and each '
            . 'code can only be used once.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'twoFAEmergencyCodeTooltip',
            'Was sind Notfall-Codes?'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAEmergencyCodeTooltip',
            'What is an emergency code?'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'twoFAEmergencyCodeDescription',
            'Mithilfe von Notfall-Codes können Sie sich auch ohne 2-Faktor-Authentifizierung in Ihrem Kundenkonto '
            . 'anmelden, obwohl Sie diese aktiviert haben. '
            . 'Dies ist z. B. notwendig, wenn Sie Ihr Smartphone verloren haben oder versehentlich '
            . 'die Authenticator App von Ihrem Smartphone gelöscht haben.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAEmergencyCodeDescription',
            'You can use emergency codes to log in to your customer account without two-factor authentication, '
            . 'even though you have activated it. This might be necessary if you have lost your smartphone '
            . 'or accidentally deleted the authenticator app from your smartphone.'
        );

        $this->setLocalization(
            'ger',
            'account data',
            'twoFAEnableConfirmTitle',
            'Wollen Sie wirklich die 2-Faktor-Authentifizierung aktivieren?'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAEnableConfirmTitle',
            'Are you sure you want to enable two-factor authentication?'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'twoFAEnableConfirmMessage',
            'Wenn Sie die 2-Faktor-Authentifizierung aktivieren, '
            . 'können Sie sich nur noch mithilfe Ihres Smartphones anmelden.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAEnableConfirmMessage',
            'If you enable two-factor authentication, you can only log in using your smartphone.'
        );

        $this->setLocalization(
            'ger',
            'account data',
            'twoFAtutorialTitle',
            'Kundenkonto mit der Authenticator App verbinden'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAtutorialTitle',
            ''
        );
        $this->setLocalization(
            'ger',
            'account data',
            'twoFAtutorialStep1',
            'Öffnen Sie die Authenticator App Ihrer Wahl.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAtutorialStep1',
            'Open the authenticator app of your choice.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'twoFAtutorialStep2',
            'Scannen Sie mit der Authenticator App den unten stehenden QR-Code.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAtutorialStep2',
            'Use the authenticator app to scan the QR code below.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'twoFAtutorialStep3',
            'Sie haben die Authenticator App erfolgreich mit Ihrem Kundenkonto verbunden.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAtutorialStep3',
            'You have successfully connected the authenticator app to your customer account.'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'twoFAAuthCodeTooltip',
            'Wo finde ich den Authentifizierungs-Code?'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAAuthCodeTooltip',
            'Where do I find the authentication code?'
        );
        $this->setLocalization(
            'ger',
            'account data',
            'twoFAAuthCodeTooltipDescription',
            'Der Authentifizierungs-Code wird Ihnen in der Authenticator App angezeigt.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'twoFAAuthCodeTooltipDescription',
            'The authentication code is displayed in the authenticator app.'
        );
        $this->setLocalization('ger', 'account data', 'twoFactorAuthCode', 'Authentifizierungs-Code/Notfall-Code');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('twoFAAuthCodeTooltip', 'account data');
        $this->removeLocalization('twoFAAuthCodeTooltipDescription', 'account data');
        $this->removeLocalization('twoFAEnableWarning', 'account data');
        $this->removeLocalization('twoFAAppWarning', 'account data');
        $this->removeLocalization('twoFAEmergencyCodesNotice', 'account data');
        $this->removeLocalization('twoFAEmergencyCodeTooltip', 'account data');
        $this->removeLocalization('twoFAEmergencyCodeDescription', 'account data');
        $this->removeLocalization('twoFAEnableConfirmTitle', 'account data');
        $this->removeLocalization('twoFAEnableConfirmMessage', 'account data');
        $this->removeLocalization('twoFAtutorialTitle', 'account data');
        $this->removeLocalization('twoFAtutorialStep1', 'account data');
        $this->removeLocalization('twoFAtutorialStep2', 'account data');
        $this->removeLocalization('twoFAtutorialStep3', 'account data');
        $this->setLocalization('ger', 'account data', 'twoFactorAuthCode', 'Auth-Code/Notfall-Code');
    }
}
