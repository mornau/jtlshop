<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20240523123200
 */
class Migration20240523123200 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fm';
    }

    public function getDescription(): string
    {
        return 'Create session invalidation timestamp for customers';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE `tkunde`
                ADD COLUMN `dSessionInvalidate` DATETIME NULL DEFAULT NULL'
        );
        $this->removeLocalization('changePasswordDesc', 'login');
        $this->setLocalization(
            'ger',
            'login',
            'changePasswordDesc',
            'Bitte beachten Sie, dass Sie aus allen aktiven Sessions automatisch ausgeloggt werden, '
            . 'nachdem Sie das Passwort geändert haben.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'changePasswordDesc',
            'Please note that you will be automatically logged out of all active sessions after changing the password.'
        );
        $this->setLocalization(
            'ger',
            'login',
            'loggedOutDueToPasswordChange',
            'Sie wurden automatisch ausgeloggt, weil Ihr Passwort geändert wurde. '
            . 'Falls Sie nicht selbst Ihr Passwort geändert haben, setzen Sie Ihr Passwort über die '
            . '"Passwort vergessen"-Funktion zurück oder wenden Sie sich an den Shopbetreiber.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'loggedOutDueToPasswordChange',
            'You have been automatically logged out because your password has been changed. '
            . 'If you have not changed your password yourself, reset your password using the '
            . '"Forgot password" function or contact the shop owner.'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE `tkunde`
                DROP COLUMN `dSessionInvalidate`'
        );
        $this->removeLocalization('changePasswordDesc', 'login');
        $this->removeLocalization('loggedOutDueToPasswordChange', 'login');

        $this->setLocalization(
            'ger',
            'login',
            'changePasswordDesc',
            'Füllen Sie bitte das Formular aus, um Ihr Passwort zu ändern.'
        );
        $this->setLocalization(
            'eng',
            'login',
            'changePasswordDesc',
            'Please fill in the form in order to change your password.'
        );
    }
}
