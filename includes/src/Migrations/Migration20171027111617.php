<?php

/**
 * Rename the settings-menu entries "Einstellungen" into proper names
 *
 * @author cr
 * @created Fri, 27 Oct 2017 11:16:17 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20171027111617
 */
class Migration20171027111617 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Rename the settings-menu entries "Einstellungen" into proper names';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'UPDATE `teinstellungensektion`
                SET `cName` = \'Formulareinstellungen\'
                WHERE `cRecht` = \'SETTINGS_CUSTOMERFORM_VIEW\''
        );
        $this->execute(
            'UPDATE `teinstellungensektion`
                SET `cName` = \'Emaileinstellungen\'
                WHERE `cRecht` = \'SETTINGS_EMAILS_VIEW\''
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'UPDATE `teinstellungensektion`
                SET `cName` = \'Einstellungen\'
                WHERE `cRecht` = \'SETTINGS_CUSTOMERFORM_VIEW\''
        );
        $this->execute(
            'UPDATE `teinstellungensektion`
                SET `cName` = \'Einstellungen\'
                WHERE `cRecht` = \'SETTINGS_EMAILS_VIEW\''
        );
    }
}
