<?php

/**
 * adds option for ken burns effect to sliders
 *
 * @author ms
 * @created Mon, 24 Oct 2016 12:41:00 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20161024124100
 */
class Migration20161024124100 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tslider ADD COLUMN bUseKB TINYINT(1) NOT NULL AFTER bRandomStart;');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tslider DROP COLUMN bUseKB');
    }
}
