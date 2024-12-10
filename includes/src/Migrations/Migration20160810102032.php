<?php

/**
 * Persistent deliverytime in orders
 *
 * @author root
 * @created Wed, 10 Aug 2016 10:20:32 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20160810102032
 */
class Migration20160810102032 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE tbestellung 
                ADD COLUMN nLongestMinDelivery INT NOT NULL DEFAULT 0 AFTER cVersandInfo,
                ADD COLUMN nLongestMaxDelivery INT NOT NULL DEFAULT 0 AFTER nLongestMinDelivery'
        );
        $this->execute(
            'ALTER TABLE twarenkorbpos 
                ADD COLUMN nLongestMinDelivery INT NOT NULL DEFAULT 0,
                ADD COLUMN nLongestMaxDelivery INT NOT NULL DEFAULT 0 AFTER nLongestMinDelivery'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE tbestellung 
                DROP COLUMN nLongestMinDelivery,
                DROP COLUMN nLongestMaxDelivery'
        );
        $this->execute(
            'ALTER TABLE twarenkorbpos 
                DROP COLUMN nLongestMinDelivery,
                DROP COLUMN nLongestMaxDelivery'
        );
    }
}
