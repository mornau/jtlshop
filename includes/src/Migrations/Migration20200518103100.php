<?php

/**
 * @author mh
 * @created Mon, 18 May 2020 10:31:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Shopsetting;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20200518103100
 */
class Migration20200518103100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add newsletter active option';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        if (Shopsetting::getInstance()->getValue(\CONF_NEWSLETTER, 'newsletter_active') === null) {
            $this->setConfig(
                'newsletter_active',
                'Y',
                \CONF_NEWSLETTER,
                'Newsletter aktivieren',
                'selectbox',
                15,
                (object)[
                    'cBeschreibung' => 'Hier legen Sie fest, ob der Newsletter genutzt werden soll.',
                    'inputOptions'  => [
                        'Y' => 'Ja',
                        'N' => 'Nein',
                    ],
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('newsletter_active');
    }
}
