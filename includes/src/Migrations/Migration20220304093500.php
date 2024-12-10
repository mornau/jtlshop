<?php

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20220304093500
 */
class Migration20220304093500 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'ms';
    }

    public function getDescription(): string
    {
        return 'Add "internal" flag to campaigns';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE tkampagne
                ADD nInternal INT DEFAULT 0 NOT NULL;'
        );
        $this->execute('UPDATE tkampagne SET nInternal=1 WHERE kKampagne < 1000');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE tkampagne
                DROP COLUMN nInternal'
        );
    }
}
