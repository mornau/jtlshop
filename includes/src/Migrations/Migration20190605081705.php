<?php

/**
 * Add NL cron setting
 *
 * @author cr
 * @created Wed, 05 Jun 2019 08:17:05 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190605081705
 */
class Migration20190605081705 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'cr';
    }

    public function getDescription(): string
    {
        return 'Add NL cron setting';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'newsletter_send_delay',
            '1',
            \CONF_NEWSLETTER,
            'Newsletter Sendeverzögerung',
            'number',
            130,
            (object)[
                'cBeschreibung'     => 'Legt die Wartezeit (in Stunden) zwischen den Newsletter-Sendungen fest.',
                'nStandardAnzeigen' => 1
            ],
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('newsletter_send_delay');
    }
}
