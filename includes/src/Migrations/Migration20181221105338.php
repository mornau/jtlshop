<?php

/**
 * add_conf_min_stock_for_availability_notifictaion
 *
 * @author mh
 * @created Fri, 21 Dec 2018 10:53:38 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20181221105338
 */
class Migration20181221105338 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'mh';
    }

    public function getDescription(): string
    {
        return 'Add conf min stock for availability notifictaion';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->setConfig(
            'benachrichtigung_min_lagernd',
            0,
            5,
            'Mindestlagerbestand für Benachrichtigung',
            'number',
            745
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('benachrichtigung_min_lagernd');
    }
}
