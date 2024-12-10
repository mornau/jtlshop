<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20230228174500
 */
class Migration20230228174500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'dr';
    }

    public function getDescription(): string
    {
        return 'Add flag to tbestseller, that indicates if it is considered as such';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE tbestseller ADD COLUMN isBestseller TINYINT NOT NULL DEFAULT 0');
        $this->execute('UPDATE tbestseller SET isBestseller = 1');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE tbestseller DROP COLUMN isBestseller');
    }
}
