<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240524145700
 */
class Migration20240524145700 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Create 2FA frontend feature';
    }

    public function up(): void
    {
        $this->upSql();
        $this->upTranslations();
        $this->upConfig();
    }

    private function upConfig(): void
    {
        $this->setConfig(
            'enable_2fa',
            'N',
            \CONF_KUNDEN,
            '2-Faktor-Authentifizierung aktivieren',
            'selectbox',
            235,
            (object)[
                'cBeschreibung' => 'Soll 2-Faktor-Authentifizierung für Kunden aktiviert werden?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ]
        );
    }

    private function upTranslations(): void
    {
        $this->setLocalization('ger', 'account data', 'save', 'Speichern');
        $this->setLocalization('eng', 'account data', 'save', 'Save');

        $this->setLocalization('ger', 'account data', 'close', 'Schließen');
        $this->setLocalization('eng', 'account data', 'close', 'Close');

        $this->setLocalization('ger', 'account data', 'print', 'Drucken');
        $this->setLocalization('eng', 'account data', 'print', 'Print');

        $this->setLocalization('ger', 'account data', 'account', 'Konto');
        $this->setLocalization('eng', 'account data', 'account', 'Account');

        $this->setLocalization('ger', 'account data', 'shop', 'Shop');
        $this->setLocalization('eng', 'account data', 'shop', 'Shop');

        $this->setLocalization('ger', 'account data', 'emergencyCodes', 'Notfall-Codes');
        $this->setLocalization('eng', 'account data', 'emergencyCodes', 'Emergency codes');

        $this->setLocalization('ger', 'account data', 'codeCreateAgain', 'Neue Codes erzeugen');
        $this->setLocalization('eng', 'account data', 'codeCreateAgain', 'Create new codes');

        $this->setLocalization(
            'ger',
            'account data',
            'enableTwoFAwarning',
            'Bitte beachten Sie, dass Sie nach dem Speichern mit diesem Benutzerkonto keine Möglichkeit mehr haben, '
            . 'sich einzuloggen, falls Sie keinen Zugriff mehr auf die '
            . 'Authenticator App auf Ihrem Mobilgerät haben sollten!'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'enableTwoFAwarning',
            'Please note that after saving, you can no longer access your account if you do not have access to '
            . 'the Authenticator App via a mobile device.'
        );

        $this->setLocalization('ger', 'account data', 'enableTwoFA', '2-Faktor-Authentifizierung aktivieren');
        $this->setLocalization('eng', 'account data', 'enableTwoFA', 'Enable 2-Factor-Authentication');

        $this->setLocalization('ger', 'account data', 'manageTwoFA', '2-Faktor-Authentifizierung bearbeiten');
        $this->setLocalization('eng', 'account data', 'manageTwoFA', 'Manage 2-Factor-Authentication');

        $this->setLocalization(
            'ger',
            'account data',
            'manageTwoFADesc',
            'Hier können Sie die 2-Faktor-Authentifizierung aktivieren, deaktivieren und bearbeiten'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'manageTwoFADesc',
            'Here you can enable, disable and manage 2-Factor-Authentication'
        );

        $this->setLocalization(
            'ger',
            'account data',
            'clickHereToCreateQR',
            'Um einen neuen QR-Code zu erzeugen, klicken Sie bitte hier:'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'clickHereToCreateQR',
            'To generate a new QR code, please click here:'
        );

        $this->setLocalization('ger', 'account data', 'emergencyCodeCreate', 'Neue Notfall-Codes erzeugen');
        $this->setLocalization('eng', 'account data', 'emergencyCodeCreate', 'Generate new emergency codes');

        $this->setLocalization('ger', 'account data', 'codeCreate', 'Neuen Code erzeugen');
        $this->setLocalization('eng', 'account data', 'codeCreate', 'Generate new code');

        $this->setLocalization(
            'ger',
            'account data',
            'infoScanQR',
            'Scannen Sie den hier abgebildeten QR-Code mit der Authenticator App auf Ihrem Handy.'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'infoScanQR',
            'Scan the displayed QR code using the Authenticator App on your phone.'
        );

        $this->setLocalization('ger', 'account data', 'shopEmergencyCodes', 'Notfall-Codes für Ihr Kundenkonto');
        $this->setLocalization('eng', 'account data', 'shopEmergencyCodes', 'Emergency codes for your account.');

        $this->setLocalization('ger', 'account data', 'twoFactorAuthCode', 'Auth-Code/Notfall-Code');
        $this->setLocalization('eng', 'account data', 'twoFactorAuthCode', 'Auth code/Emergency code');

        $this->setLocalization('ger', 'account data', 'twoFactorAuthentication', '2-Faktor-Authentifizierung');
        $this->setLocalization('eng', 'account data', 'twoFactorAuthentication', '2-Factor-Authentication');

        $this->setLocalization(
            'ger',
            'account data',
            'warningAuthSecretOverwrite',
            'Der bisherige zweite Faktor und die Notfallcodes werden ersetzt. Möchten Sie fortfahren?'
        );
        $this->setLocalization(
            'eng',
            'account data',
            'warningAuthSecretOverwrite',
            'The former second factor and all emergency codes will be replaced. Continue?'
        );

        $this->setLocalization(
            'ger',
            'global',
            'accountSetTwoFA',
            'Zum Einloggen ist ein zweiter Faktor erforderlich.'
        );
        $this->setLocalization('eng', 'global', 'accountSetTwoFA', 'A second factor is required to log in.');

        $this->setLocalization('ger', 'global', 'accountInvalidTwoFA', 'Ungültiger Code.');
        $this->setLocalization('eng', 'global', 'accountInvalidTwoFA', 'Invalid code.');
        $this->setLocalization(
            'ger',
            'login',
            'loggedOutDueTo2FAChange',
            'Sie wurden automatisch ausgeloggt, weil Einstellungen Ihrer 2-Faktor-Authentifizierung geändert wurden.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'loggedOutDueTo2FAChange',
            'You have been automatically logged out because your two factor authentifaction settings have been changed.'
        );
    }

    private function upSql(): void
    {
        $this->execute(
            "ALTER TABLE tkunde
                ADD b2FAauth tinyint(1) DEFAULT 0,
                ADD c2FAauthSecret VARCHAR(255) DEFAULT ''"
        );
        $this->execute(
            "CREATE TABLE `tkunde2facodes` (
                `kKunde` INT(11) NOT NULL DEFAULT 0,
                `cEmergencyCode` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                UNIQUE KEY `cEmergencyCode` (`cEmergencyCode`),
                KEY `kKunde` (`kKunde`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        );
    }

    public function down(): void
    {
        $this->dropColumn('tkunde', 'b2FAauth');
        $this->dropColumn('tkunde', 'c2FAauthSecret');
        $this->execute('DROP TABLE IF EXISTS tkunde2facodes');
        $this->removeLocalization('loggedOutDueTo2FAChange', 'login');
        $this->removeLocalization('save', 'account data');
        $this->removeLocalization('shop', 'account data');
        $this->removeLocalization('account', 'account data');
        $this->removeLocalization('enableTwoFA', 'account data');
        $this->removeLocalization('manageTwoFA', 'account data');
        $this->removeLocalization('manageTwoFADesc', 'account data');
        $this->removeLocalization('enableTwoFAwarning', 'account data');
        $this->removeLocalization('infoScanQR', 'account data');
        $this->removeLocalization('codeCreateAgain', 'account data');
        $this->removeLocalization('infoScanQR', 'account data');
        $this->removeLocalization('close', 'account data');
        $this->removeLocalization('print', 'account data');
        $this->removeLocalization('codeCreate', 'account data');
        $this->removeLocalization('emergencyCodeCreate', 'account data');
        $this->removeLocalization('clickHereToCreateQR', 'account data');
        $this->removeLocalization('shopEmergencyCodes', 'account data');
        $this->removeLocalization('twoFactorAuthentication', 'account data');
        $this->removeLocalization('warningAuthSecretOverwrite', 'account data');
        $this->removeLocalization('twoFactorAuthCode', 'account data');
        $this->removeLocalization('accountSetTwoFA', 'global');
        $this->removeLocalization('accountInvalidTwoFA', 'global');

        $this->removeConfig('enable_2fa');
    }
}
