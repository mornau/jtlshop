<?php

/**
 * Create new Varkombi index
 *
 * @author fp
 * @created Tue, 19 Feb 2019 09:01:54 +0100
 */

declare(strict_types=1);

namespace JTL\Migrations;

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration20190219090154
 */
class Migration20190219090154 extends Migration implements IMigration
{
    public function getAuthor(): string
    {
        return 'fp';
    }

    public function getDescription(): string
    {
        return 'Create new Varkombi index';
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE UNIQUE INDEX idx_eigenschaftwert_uq
                ON teigenschaftkombiwert (kEigenschaft, kEigenschaftWert, kEigenschaftKombi)'
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'DROP INDEX idx_eigenschaftwert_uq ON teigenschaftkombiwert'
        );
    }
}
