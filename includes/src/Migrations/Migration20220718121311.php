<?php

/**
 * Add text field to ttemplateeinstellungen.
 *
 * @author fp
 * @created Mon, 18 Jul 2022 12:13:11 +0200
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220718121311
 */
class Migration20220718121311 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Add text field to ttemplateeinstellungen';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE ttemplateeinstellungen MODIFY COLUMN cWert MEDIUMTEXT NULL');
        $this->execute('ALTER TABLE teinstellungen MODIFY COLUMN cWert MEDIUMTEXT NULL');
        $this->execute('ALTER TABLE tplugineinstellungen MODIFY COLUMN cWert MEDIUMTEXT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE ttemplateeinstellungen MODIFY COLUMN cWert VARCHAR(255) NULL');
        $this->execute('ALTER TABLE teinstellungen MODIFY COLUMN cWert VARCHAR(255) NULL');
        $this->execute('ALTER TABLE tplugineinstellungen MODIFY COLUMN cWert VARCHAR(255) NULL');
    }
}
