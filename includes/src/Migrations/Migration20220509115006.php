<?php

/**
 * Add noDiscount to price table
 *
 * @author fp
 * @created Mon, 09 May 2022 11:50:06 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220509115006
 */
class Migration20220509115006 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Add noDiscount to price table';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tpreis ADD COLUMN noDiscount INT DEFAULT 0');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tpreis DROP COLUMN noDiscount');
    }
}
