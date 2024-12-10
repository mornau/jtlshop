<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Add default value for topcblueprint.kPlugin
 *
 * @author dr
 */
class Migration20190227140600 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add default value for topcblueprint.kPlugin';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE topcblueprint MODIFY kPlugin INT NOT NULL DEFAULT 0');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE topcblueprint MODIFY kPlugin INT NOT NULL');
    }
}
