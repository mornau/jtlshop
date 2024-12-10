<?php

/**
 * @author mh
 * @created Mon, 05 Jun 2020 13:09:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200604130900
 */
class Migration20200604130900 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add consent activate option';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'consent_manager_active',
            'Y',
            \CONF_CONSENTMANAGER,
            'Consent Manager aktivieren',
            'selectbox',
            100,
            (object)[
                'cBeschreibung' => 'Hier legen Sie fest, ob der Consent Manager genutzt werden soll.',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein',
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('consent_manager_active');
    }
}
