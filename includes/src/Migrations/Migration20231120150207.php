<?php

/**
 * add_config_for_consistent_gross_prices
 *
 * @author dr
 * @created Mon, 20 Nov 2023 15:02:07 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20231120150207
 */
class Migration20231120150207 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'add_config_for_consistent_gross_prices';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $defaultValue = \defined('\CONSISTENT_GROSS_PRICES') && \CONSISTENT_GROSS_PRICES === false ? 'N' : 'Y';
        $this->setConfig(
            'consistent_gross_prices',
            $defaultValue,
            \CONF_GLOBAL,
            'Gleichbleibende Bruttopreise',
            'selectbox',
            750,
            (object)[
                'inputOptions' => [
                    'Y' => 'Ja, Bruttopreise unabhängig vom Lieferland',
                    'N' => 'Nein, Bruttopreise abhängig vom Lieferland-Steuersatz',
                ]
            ],
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('consistent_gross_prices');
    }
}
